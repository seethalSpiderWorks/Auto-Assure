<?php

namespace Database\Seeders;

use App\Models\InspectionStep;
use Illuminate\Database\Seeder;

/**
 * Arabic wording for every checklist question, across all templates.
 *
 * Lookup is by a normalised key — lower case with punctuation stripped — so the
 * spelling variants the templates carry ("Hoses & pipes" / "Hoses Pipes",
 * "Excess Smoke(minor/major)" / "Excess Smoke (Minor/Major)") all resolve to the
 * same entry.
 *
 * Existing Arabic is never overwritten: a question whose question_ar is already
 * filled is left exactly as it is, so anything corrected by hand survives a
 * re-run. Questions with no entry keep their English, which the report falls
 * back to.
 *
 * These are trade terms. They read correctly as Gulf automotive Arabic, but have
 * them checked by an Arabic-speaking inspector before the wording goes out to a
 * customer — the report is the company's word, not a translation exercise.
 */
class QuestionArabicSeeder extends Seeder
{
    /** English question => Arabic. Keys are normalised on lookup. */
    private const ARABIC = [
        // ---- Performance / driving aids -------------------------------------
        'Air Suspension' => 'نظام تعليق هوائي',
        'Adaptive Air Suspension' => 'نظام تعليق هوائي متكيف',
        'Differential Lock' => 'قفل الترس التفاضلي',
        'Paddle Shifters' => 'مبدلات السرعة خلف المقود',
        'Tiptronic' => 'ناقل حركة تيبترونيك',
        'Hill Descent Assist' => 'مساعد النزول على المنحدرات',
        'Hill Start Assist' => 'مساعد الانطلاق على المرتفعات',
        'Auto Hold' => 'التثبيت التلقائي للفرامل',
        'Comfort Seats' => 'مقاعد مريحة',
        'Sport Seats' => 'مقاعد رياضية',
        'Sport Brakes' => 'فرامل رياضية',
        'Sport Suspension' => 'نظام تعليق رياضي',
        'Sport Exhaust' => 'عادم رياضي',
        'Lane Change' => 'مساعد تغيير المسار',
        'Assist Launch Control' => 'نظام الانطلاق السريع',

        // ---- Safety ---------------------------------------------------------
        'Child Safety Seats (ISOFIX)' => 'تثبيت مقاعد الأطفال (ISOFIX)',
        'Front View Camera' => 'الكاميرا الأمامية',
        'Rear View Camera' => 'كاميرا الرؤية الخلفية',
        '360 Degree Camera' => 'كاميرا محيطية 360 درجة',
        'Front Parking Sensors' => 'حساسات الركن الأمامية',
        'Rear Parking Sensors' => 'حساسات الركن الخلفية',
        'Parking Sensors' => 'حساسات الركن',
        'Lane Departure' => 'التنبيه عند مغادرة المسار',
        'Anti-Lock Brakes (ABS)' => 'نظام منع انغلاق الفرامل (ABS)',
        'EBD' => 'التوزيع الإلكتروني لقوة الفرملة (EBD)',
        'Alarm' => 'جهاز الإنذار',
        'Front Airbags' => 'الوسائد الهوائية الأمامية',
        'Side Airbags' => 'الوسائد الهوائية الجانبية',
        'Traction Control System' => 'نظام التحكم بالجر',
        'Park Assist' => 'مساعد الركن',
        'Blind Spot Monitor' => 'مراقبة النقطة العمياء',
        'Tire Pressure Monitor' => 'مراقبة ضغط الإطارات',
        'Tyre Pressure Monitor' => 'مراقبة ضغط الإطارات',
        'Anti Glare Rear View Mirror' => 'مرآة داخلية مانعة للانبهار',
        'Anti-Glare Rear View Mirror' => 'مرآة داخلية مانعة للانبهار',

        // ---- Interior / entertainment ---------------------------------------
        'Digital Driver Display' => 'شاشة عدادات رقمية',
        'CD Player' => 'مشغل أقراص CD',
        'DVD Player' => 'مشغل أقراص DVD',
        'MP3 Player' => 'مشغل MP3',
        'SD Card Player' => 'مشغل بطاقة SD',
        'Bluetooth Interface' => 'اتصال بلوتوث',
        'Premium Sound System' => 'نظام صوتي متميز',
        'AUX Audio System' => 'مدخل صوت AUX',
        'USB' => 'منفذ USB',
        'USB-C' => 'منفذ USB-C',
        'Touch Screen' => 'شاشة تعمل باللمس',
        'Touchscreen' => 'شاشة تعمل باللمس',
        'Rear Seat Entertain. Sys' => 'نظام ترفيه المقاعد الخلفية',
        'Wireless' => 'شحن لاسلكي',
        'Ambient Lighting' => 'الإضاءة المحيطة',
        'Apple CarPlay' => 'أبل كار بلاي',
        'Navigation' => 'نظام الملاحة',
        'Standard AC' => 'تكييف عادي',
        'Standard A/C' => 'تكييف عادي',
        'Dual-Zone Climate Ctrl AC' => 'تكييف مناخي ثنائي المناطق',
        'Multi-Zone Climate Ctrl AC' => 'تكييف مناخي متعدد المناطق',
        'Keyless Entry' => 'دخول بدون مفتاح',
        'Keyless Start' => 'تشغيل بدون مفتاح',
        'Power Steering' => 'مقود معزز (باور)',
        'Heads Up Display' => 'شاشة العرض الأمامية (HUD)',
        'Cruise Control' => 'مثبت السرعة',
        'Adaptive Cruise Control' => 'مثبت السرعة المتكيف',
        'Seat Cooling Front' => 'تبريد المقاعد الأمامية',
        'Seat Cooling Rear' => 'تبريد المقاعد الخلفية',
        'Seat Massage Front' => 'مساج المقاعد الأمامية',
        'Seat Massage Rear' => 'مساج المقاعد الخلفية',
        'Seat Heated Front' => 'تدفئة المقاعد الأمامية',
        'Driver Memory Seat' => 'ذاكرة مقعد السائق',
        'Passenger Memory Seat' => 'ذاكرة مقعد الراكب',
        'Power Driver Seats' => 'مقعد سائق كهربائي',
        'Power Passenger Seats' => 'مقعد راكب كهربائي',
        'Power Rear Seats' => 'مقاعد خلفية كهربائية',
        'Power Front Windows' => 'نوافذ أمامية كهربائية',
        'Power Rear Windows' => 'نوافذ خلفية كهربائية',
        'Power Trunk' => 'صندوق خلفي كهربائي',
        'Power Locks' => 'أقفال كهربائية',
        'Power Mirrors' => 'مرايا كهربائية',
        'Power Folding Mirrors' => 'مرايا كهربائية قابلة للطي',
        'Sun Roof' => 'فتحة سقف',
        'Sunroof' => 'فتحة سقف',
        'Panoramic Roof' => 'سقف بانورامي',
        'Cool Box' => 'صندوق تبريد',
        'Auto Park' => 'الركن الآلي',
        'Remote Start Engine' => 'تشغيل المحرك عن بُعد',
        'Soft Close Doors' => 'أبواب ذاتية الإغلاق',
        'Adaptive Lights' => 'إضاءة متكيفة',
        'Night Vision' => 'نظام الرؤية الليلية',
        'Captain Rear Seats' => 'مقاعد خلفية منفصلة',
        'Leather Seats' => 'مقاعد جلد',
        'Fabric Seats' => 'مقاعد قماش',
        'Body Kit' => 'طقم هيكل خارجي',
        'Lift Kit' => 'طقم رفع',
        'Front Spoiler' => 'جناح أمامي',
        'Rear Spoiler' => 'جناح خلفي',
        'Fog Light Front' => 'أضواء الضباب الأمامية',
        'Front Fog Lights' => 'أضواء الضباب الأمامية',
        'Roof Carrier Front' => 'حمالة السقف الأمامية',
        'Halogen Headlight' => 'مصابيح هالوجين',
        'LED Headlight' => 'مصابيح LED',
        'Xenon Headlight' => 'مصابيح زينون',
        'Trailer Hook Coupling' => 'وصلة القطر',
        'Winch' => 'ونش سحب',
        'Fire extinguisher' => 'طفاية حريق',

        // ---- Exterior -------------------------------------------------------
        'Fuel filler cover/Petrol Cap' => 'غطاء خزان الوقود',
        'Fuel Filler Cover / Petrol' => 'غطاء خزان الوقود',
        'Door locks / operation' => 'أقفال الأبواب وطريقة عملها',
        'Glass' => 'الزجاج',
        'Molding' => 'الشرائح الجانبية',
        'Bumper Grills' => 'شبك الصدام',
        'Front bumper' => 'الصدام الأمامي',
        'Rear bumper' => 'الصدام الخلفي',
        'Front left headlights' => 'المصباح الأمامي الأيسر',
        'Front right headlights' => 'المصباح الأمامي الأيمن',
        'Rear left tail lights' => 'المصباح الخلفي الأيسر',
        'Rear right tail lights' => 'المصباح الخلفي الأيمن',
        'General body condition' => 'الحالة العامة للهيكل',
        'Exterior condition' => 'حالة الهيكل الخارجي',

        // ---- Interior -------------------------------------------------------
        'Seat belts' => 'أحزمة الأمان',
        'Headliner' => 'بطانة السقف',
        'Rearview mirror' => 'المرآة الداخلية',
        'Steering wheel' => 'عجلة القيادة',
        'Gear lever' => 'عصا ناقل الحركة',
        'Sun visor' => 'حاجب الشمس',
        'Pillar trim' => 'بطانة القوائم',
        'Armrest console' => 'مسند الذراع الأوسط',
        'Floor mats and carpets' => 'الدواسات والسجاد',
        'Floor Mats & Carpets' => 'الدواسات والسجاد',
        'Trunk liner' => 'بطانة الصندوق',
        'Dashboard' => 'لوحة القيادة (التابلوه)',
        'Glove compartment' => 'صندوق القفازات',
        'Seats' => 'المقاعد',
        'Door trims' => 'بطانة الأبواب',
        'A/C grills' => 'فتحات التكييف',
        'Sunroof shade / Sunroof liner' => 'ستارة فتحة السقف',
        'Sunroof Shade Liner' => 'ستارة فتحة السقف',
        'Interior condition' => 'حالة المقصورة الداخلية',

        // ---- Tyres ----------------------------------------------------------
        'Spare Tyre' => 'الإطار الاحتياطي',
        'Front Left Tyre' => 'الإطار الأمامي الأيسر',
        'Front Right Tyre' => 'الإطار الأمامي الأيمن',
        'Back Left Tyre' => 'الإطار الخلفي الأيسر',
        'Back Right Tyre' => 'الإطار الخلفي الأيمن',

        // ---- Engine ---------------------------------------------------------
        'Coolant level' => 'مستوى سائل التبريد',
        'Coolant leaks' => 'تسريب سائل التبريد',
        'Coolant Conditions' => 'حالة سائل التبريد',
        'Steering fluid' => 'زيت المقود',
        'Brake master and booster' => 'المضخة الرئيسية وبوستر الفرامل',
        'Evidence of overheating' => 'آثار ارتفاع حرارة المحرك',
        'Radiator cap' => 'غطاء الرادييتر',
        'Radiator fan' => 'مروحة الرادييتر',
        'Fender liner' => 'بطانة الرفرف',
        'Hoses & pipes' => 'الخراطيم والأنابيب',
        'Hoses Pipes' => 'الخراطيم والأنابيب',
        'Cable, harnes & connectors' => 'الأسلاك والوصلات الكهربائية',
        'Cables, Harness & Connectors' => 'الأسلاك والوصلات الكهربائية',
        'Power steering fluid level' => 'مستوى زيت المقود',
        'Engine oil level' => 'مستوى زيت المحرك',
        'External engine leaks' => 'تسريبات خارجية من المحرك',
        'Engine mounts' => 'كراسي المحرك',
        'Turbo/ Supercharger' => 'التيربو / الشاحن الفائق',
        'Fuel pump & pipes' => 'مضخة الوقود والأنابيب',
        'Cold starting' => 'التشغيل على البارد',
        'Fast idle when engine cold' => 'ارتفاع دوران المحرك على البارد',
        'Fast Idle When The Engine Cold' => 'ارتفاع دوران المحرك على البارد',
        'Noise lvl whn engine cold' => 'مستوى الصوت عند التشغيل البارد',
        'Noise Level When The Engine Cold' => 'مستوى الصوت عند التشغيل البارد',
        'Excess Smoke(minor/major)' => 'دخان زائد (بسيط / كبير)',
        'Excess Smoke (Minor/Major)' => 'دخان زائد (بسيط / كبير)',
        'Inlet manifold' => 'مشعب السحب',
        'Outlet manifold' => 'مشعب العادم',
        'Exhaust Pipes' => 'أنابيب العادم',
        'Silencer' => 'كاتم الصوت',
        'Silencer(s)' => 'كاتم الصوت',
        'Head shields & mountings' => 'الدروع الحرارية والتثبيتات',
        'Joints & couplings' => 'الوصلات والمفاصل',
        'Engine underside leaks' => 'تسريبات أسفل المحرك',
        'Catalytic converter' => 'المحول الحفاز',
        'Engine shield' => 'درع حماية المحرك',
        'Engine condition' => 'حالة المحرك',

        // ---- Transmission ---------------------------------------------------
        'Gear selector' => 'محدد نقل الحركة',
        'Gear shifting' => 'تبديل السرعات',
        'Gear noise' => 'صوت ناقل الحركة',
        'Fluid Level & Oil Leak' => 'مستوى الزيت والتسريب',
        'Transmission Mount (Gear Mount)' => 'كرسي ناقل الحركة',
        'Transmission' => 'ناقل الحركة',

        // ---- Electrical -----------------------------------------------------
        'Door locks' => 'أقفال الأبواب',
        'Door Locks (which side)' => 'أقفال الأبواب (أي جهة)',
        'Central Locking' => 'القفل المركزي',
        'Ignition lock/Starting sys' => 'قفل التشغيل ونظام البدء',
        'Ignition Lock / Starting System' => 'قفل التشغيل ونظام البدء',
        'Instrument panel' => 'لوحة العدادات',
        'Headlights' => 'المصابيح الأمامية',
        'Sidelights / Running lights' => 'الأضواء الجانبية والنهارية',
        'Rear lights' => 'المصابيح الخلفية',
        'Indicator / Hazard lights' => 'إشارات الانعطاف والتحذير',
        'Boot / Tailgate lock' => 'قفل الصندوق / الباب الخلفي',
        'Reverse lights' => 'أضواء الرجوع للخلف',
        'Fog lights' => 'أضواء الضباب',
        'Multimedia' => 'نظام الوسائط المتعددة',
        'A/C Control & Cooling' => 'التحكم بالتكييف والتبريد',
        'Side Mirror' => 'المرآة الجانبية',
        'Auxiliary lights' => 'الأضواء الإضافية',
        'Panel lights' => 'إضاءة لوحة العدادات',
        'Horn' => 'المنبه',
        'Window operation' => 'عمل النوافذ',
        'Sunroof operation' => 'عمل فتحة السقف',
        'Wipers / Jet washers' => 'المساحات ورشاشات الماء',
        'Wipers Jet Washers' => 'المساحات ورشاشات الماء',
        'Keys & remote controls' => 'المفاتيح وأجهزة التحكم',
        'Keys Remote Controls' => 'المفاتيح وأجهزة التحكم',
        'Warning lights' => 'لمبات التحذير',
        'Number plate lights' => 'إضاءة لوحة الأرقام',
        'Number Plate Light' => 'إضاءة لوحة الأرقام',

        // ---- Underbody ------------------------------------------------------
        'Steering joints & ball joints' => 'مفاصل المقود والبيادات',
        'Steering Joints and Ball Joints' => 'مفاصل المقود والبيادات',
        'Brakes lines' => 'خطوط الفرامل',
        'Subframe' => 'الشاسيه الفرعي',
        'Wheels, hubs & bearings' => 'العجلات والمحاور والرمانات',
        'Wheels, Hubs, and Bearings' => 'العجلات والمحاور والرمانات',
        'Dampers and bushes' => 'المساعدات والجلب',
        'Power steering/ rack' => 'مقود الباور / علبة المقود',
        'Power Steering/ Steering Rack' => 'مقود الباور / علبة المقود',
        'Evidence of floor/chassis corrosion' => 'آثار صدأ الأرضية / الشاسيه',
        'Undercarriage / chassis condition' => 'حالة الهيكل السفلي والشاسيه',

        // ---- Test drive -----------------------------------------------------
        'Engine - Performance' => 'أداء المحرك',
        'Gearbox operation' => 'عمل ناقل الحركة',
        'Clutch operation' => 'عمل القابض (الكلتش)',
        'Steering Operation' => 'عمل المقود',
        'Brake Operation' => 'عمل الفرامل',
        'Brakes' => 'الفرامل',
        'Hand brake/ Parking brake' => 'فرامل اليد / الركن',
        'DriveTrain(4WD,2WD,AWD)' => 'نظام الدفع (4WD / 2WD / AWD)',
        'Drive Train (4WD,2WD,AWD)' => 'نظام الدفع (4WD / 2WD / AWD)',
        'Instrument & cntrl functng' => 'عمل العدادات وأدوات التحكم',
        'Instruments and Controls Functioning' => 'عمل العدادات وأدوات التحكم',
        'Suspension noise' => 'صوت نظام التعليق',
        'Road holding stability' => 'ثبات السيارة على الطريق',
        'Noise' => 'الأصوات',
        'Shock absorber' => 'ممتص الصدمات',
        'Road test' => 'اختبار الطريق',
    ];

    public function run(): void
    {
        $dictionary = [];
        foreach (self::ARABIC as $english => $arabic) {
            $dictionary[self::key($english)] = $arabic;
        }

        $filled = 0;
        $kept = 0;
        $missing = [];

        foreach (InspectionStep::all() as $step) {
            if (filled($step->question_ar)) {
                $kept++;

                continue;
            }

            $arabic = $dictionary[self::key($step->question)] ?? null;

            if (! $arabic) {
                $missing[] = $step->question;

                continue;
            }

            $step->forceFill(['question_ar' => $arabic])->save();
            $filled++;
        }

        $this->command?->info("Arabic questions — {$filled} filled, {$kept} already had Arabic.");

        if ($missing) {
            $this->command?->warn('  No entry for: '.implode(', ', array_unique($missing)));
        }
    }

    /** Lower case, punctuation stripped, spaces collapsed. */
    private static function key(string $question): string
    {
        return trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9]+/i', ' ', mb_strtolower($question))));
    }
}
