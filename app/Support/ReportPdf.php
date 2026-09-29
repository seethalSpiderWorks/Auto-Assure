<?php

namespace App\Support;

use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Prints the inspection report to a PDF with headless Chrome, so the customer
 * gets a real download instead of the browser's print dialog.
 *
 * Chrome is given the rendered HTML as a local file rather than the report URL:
 * `php artisan serve` handles one request at a time, so Chrome asking the same
 * server for the page (and its images) while this request waits would hang.
 * Every image the report uses — photos, thumbnails, the vehicle image, damage
 * diagrams, logos — is a static file under public/, so links to this site are
 * pointed at those files on disk. Fonts and scripts still come from their CDNs.
 *
 * The Chrome binary is CHROME_PATH (config services.chrome.path) when set,
 * otherwise the first of the usual install locations that exists.
 */
class ReportPdf
{
    private const CANDIDATES = [
        '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        '/Applications/Chromium.app/Contents/MacOS/Chromium',
        '/usr/bin/google-chrome',
        '/usr/bin/google-chrome-stable',
        '/usr/bin/chromium',
        '/usr/bin/chromium-browser',
        '/snap/bin/chromium',
    ];

    /**
     * @return string the PDF bytes
     */
    public static function fromHtml(string $html): string
    {
        $binary = self::binary();

        $dir = storage_path('app/report-pdf');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $id = Str::random(16);
        $htmlFile = "{$dir}/{$id}.html";
        $pdfFile = "{$dir}/{$id}.pdf";
        $profile = "{$dir}/{$id}-profile";

        file_put_contents($htmlFile, self::localise($html));

        try {
            $process = new Process([
                $binary,
                '--headless=new',
                '--disable-gpu',
                '--no-sandbox',
                '--no-first-run',
                '--hide-scrollbars',
                '--allow-file-access-from-files',
                '--no-pdf-header-footer',
                '--user-data-dir='.$profile,
                // Lets web fonts and images finish loading before printing.
                '--virtual-time-budget=10000',
                '--print-to-pdf='.$pdfFile,
                'file://'.$htmlFile,
            ]);
            // Headless Chrome writes the PDF within seconds but does not always exit
            // afterwards, so wait for the file rather than for the process: once it
            // exists and its size has stopped changing, it is complete.
            $process->start();

            $deadline = microtime(true) + 60;
            $lastSize = -1;
            while (microtime(true) < $deadline) {
                clearstatcache(true, $pdfFile);
                $size = is_file($pdfFile) ? filesize($pdfFile) : 0;

                if ($size > 0 && $size === $lastSize) {
                    break;
                }
                if (! $process->isRunning() && $size === 0) {
                    break; // exited without a PDF
                }

                $lastSize = $size;
                usleep(300000);
            }

            $process->stop(0);

            clearstatcache(true, $pdfFile);
            if (! is_file($pdfFile) || filesize($pdfFile) === 0) {
                throw new RuntimeException('Chrome did not produce a PDF: '.trim($process->getErrorOutput()));
            }

            return file_get_contents($pdfFile);
        } finally {
            @unlink($htmlFile);
            @unlink($pdfFile);
            self::removeDir($profile);
        }
    }

    /**
     * Point this site's URLs at the files under public/, and load every image
     * up front (lazy images below the fold may never load in a print).
     */
    private static function localise(string $html): string
    {
        $public = 'file://'.rtrim(public_path(), '/').'/';

        $bases = array_unique(array_filter([
            rtrim(url('/'), '/').'/',
            rtrim((string) config('app.url'), '/').'/',
        ], fn ($b) => $b !== '/'));

        foreach ($bases as $base) {
            $html = str_replace($base, $public, $html);
        }

        return str_replace(' loading="lazy"', '', $html);
    }

    private static function binary(): string
    {
        $configured = config('services.chrome.path');
        if ($configured) {
            if (! is_executable($configured)) {
                throw new RuntimeException("CHROME_PATH is set to {$configured}, which is not an executable.");
            }

            return $configured;
        }

        foreach (self::CANDIDATES as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        throw new RuntimeException('Google Chrome / Chromium was not found. Install it or set CHROME_PATH in .env.');
    }

    private static function removeDir(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() && ! $item->isLink() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
        }
        @rmdir($dir);
    }
}
