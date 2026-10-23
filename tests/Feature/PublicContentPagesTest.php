<?php

use App\Enums\GradeLevel;
use App\Enums\PostCategory;
use App\Models\GalleryImage;
use App\Models\Major;
use App\Models\MajorCourse;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

/*
|--------------------------------------------------------------------------
| Public content pages
|--------------------------------------------------------------------------
|
| The rendering contract of the functional pages: every page answers with
| 200, shows its own content in Persian and exposes the markup the
| progressive enhancement relies on (the form contract, the filters, the
| pagination fallback and the lightbox triggers).
|
| The JSON behaviour of the two form endpoints is already covered by
| ContactFormTest and AdmissionFormTest and is not repeated here.
|
*/

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->networkMajor = Major::factory()->create([
        'title' => 'شبکه و نرم‌افزار رایانه',
        'slug' => 'network-and-software',
        'icon' => 'network',
        'accent' => 'brand',
        'subtitle' => 'دانش بنیان در حوزه فناوری اطلاعات',
        'seats' => 30,
    ]);

    $this->mechatronicsMajor = Major::factory()->create([
        'title' => 'مکاترونیک',
        'slug' => 'mechatronics',
        'icon' => 'mechatronics',
        'accent' => 'accent',
        'seats' => 30,
    ]);

    MajorCourse::factory()
        ->for($this->networkMajor)
        ->forGrade(GradeLevel::Tenth)
        ->create([
            'title' => 'مبانی شبکه و سخت‌افزار',
            'weekly_hours' => 3,
        ]);

    MajorCourse::factory()
        ->for($this->mechatronicsMajor)
        ->forGrade(GradeLevel::Eleventh)
        ->create([
            'title' => 'کنترل صنعتی و PLC',
            'weekly_hours' => 4,
        ]);

    Post::factory()->create([
        'title' => 'خبر پیش‌فرض برای صفحه‌های عمومی',
        'slug' => 'public-content-default-slug',
    ]);
});

test('the majors page shows every major with its course count and a link to its page', function () {
    $response = $this->get(route('majors.index'));

    $response->assertOk();
    $response->assertSee('رشته‌های تحصیلی');
    $response->assertSee('شبکه و نرم‌افزار رایانه');
    $response->assertSee('مکاترونیک');
    $response->assertSee('دانش بنیان در حوزه فناوری اطلاعات');
    $response->assertSee('ظرفیت اعلام‌شده');
    $response->assertSee('چرا این رشته‌ها؟', false);
    $response->assertSee(route('majors.show', $this->networkMajor->slug), false);
});

test('a major page shows the description and the curriculum of every grade level', function () {
    $response = $this->get(route('majors.show', $this->networkMajor->slug));

    $response->assertOk();
    $response->assertSee('شبکه و نرم‌افزار رایانه');
    $response->assertSee('مبانی شبکه و سخت‌افزار');
    $response->assertSee('پایهٔ دهم');
    $response->assertSee('پایهٔ یازدهم');
    $response->assertSee('پایهٔ دوازدهم');
    $response->assertSee('ساعت در هفته');
    $response->assertSee('درخواست مشاوره و ثبت‌نام');
    $response->assertSee(route('majors.index'), false);
});

test('the education page lists the courses of both majors for every grade level', function () {
    $response = $this->get(route('education'));

    $response->assertOk();
    $response->assertSee('برنامهٔ درسی');
    $response->assertSee('مبانی شبکه و سخت‌افزار');
    $response->assertSee('کنترل صنعتی و PLC');
    $response->assertSee('پایهٔ دهم');
    $response->assertSee('پایهٔ یازدهم');
    $response->assertSee('پایهٔ دوازدهم');
    $response->assertSee('ساعت در هفته');
    $response->assertSee('شبکه و نرم‌افزار رایانه');
    $response->assertSee('مکاترونیک');
});

test('the gallery page renders the album filters and the lightbox triggers', function () {
    GalleryImage::factory()->create([
        'title' => 'کلاس درس شبکه',
        'album' => 'آموزشی',
        'path' => '/images/gallery/placeholder-1.svg',
    ]);

    $response = $this->get(route('gallery'));

    $response->assertOk();
    $response->assertSee('گالری تصاویر');
    $response->assertSee('data-filter="all"', false);
    $response->assertSee('data-filter="آموزشی"', false);
    $response->assertSee('data-lightbox', false);
    $response->assertSee('data-filter-item', false);
    $response->assertSee('کلاس درس شبکه');
    $response->assertSee('جای‌نگهدار', false);
});

test('the news page renders the list, the category filter and the pagination fallback', function () {
    Post::factory()->create([
        'title' => 'گزارش آزمایشی فهرست اخبار',
        'slug' => 'public-content-news-slug',
        'category' => PostCategory::Announcement,
        // Pinned to the newest slot: the factory randomises published_at, and a
        // random date could push this post onto the second page.
        'published_at' => now(),
    ]);

    /* More than one page of posts, so the pagination and the load-more
       button are part of the markup. */
    foreach (range(1, 11) as $index) {
        Post::factory()->create([
            'title' => 'خبر تکمیلی فهرست اخبار '.$index,
            'slug' => 'public-content-filler-slug-'.$index,
            'published_at' => now()->subDays(60 - $index),
        ]);
    }

    $response = $this->get(route('news.index'));

    $response->assertOk();
    $response->assertSee('اخبار و اطلاعیه‌ها');
    $response->assertSee('گزارش آزمایشی فهرست اخبار');
    $response->assertSee('اطلاعیه');
    /* The filter button carries the machine value of the category; its
       Persian label is the text of the button. */
    $response->assertSee('data-news-filter="announcement"', false);
    $response->assertSee('data-news-filter-label="اطلاعیه"', false);
    $response->assertSee('data-news-list', false);
    $response->assertSee('data-news-more', false);
    $response->assertSee('data-news-pagination', false);
});

test('the ajax posts endpoint returns the same partial used by the news page', function () {
    foreach (range(1, 20) as $index) {
        Post::factory()->create([
            'title' => 'خبر محتوای عمومی شماره '.$index,
            'slug' => 'public-content-list-slug-'.$index,
            'published_at' => now()->subDays(40 - $index),
        ]);
    }

    $response = $this->get(route('news.more', ['page' => 2]));

    $response->assertOk();
    $response->assertSee('data-news-item', false);
    $response->assertSee('data-news-next-page', false);
});

test('a news article shows its body, its category and the related posts', function () {
    $post = Post::factory()->create([
        'title' => 'مطلب آزمایشی برای صفحهٔ خبر',
        'slug' => 'public-content-article-slug',
        'category' => PostCategory::Achievement,
        'body' => 'بند نخست متن خبر.'."\n\n".'بند دوم متن خبر.',
    ]);

    Post::factory()->create([
        'title' => 'خبر دیگر برای بخش اخبار دیگر',
        'slug' => 'public-content-related-slug',
    ]);

    $response = $this->get(route('news.show', $post->slug));

    $response->assertOk();
    $response->assertSee('مطلب آزمایشی برای صفحهٔ خبر');
    $response->assertSee('افتخارآفرینی');
    $response->assertSee('بند نخست متن خبر.');
    $response->assertSee('بند دوم متن خبر.');
    $response->assertSee('اخبار دیگر');
    $response->assertSee('خبر دیگر برای بخش اخبار دیگر');
    $response->assertSee('بازگشت به فهرست اخبار');
});

test('the admission page renders the request form with every field', function () {
    $response = $this->get(route('admission.index'));

    $response->assertOk();
    $response->assertSee('درخواست مشاوره و ثبت‌نام');
    $response->assertSee('data-app-form', false);
    $response->assertSee('action="'.route('admission.store', [], false).'"', false);
    $response->assertSee('name="_token"', false);

    foreach (['student_name', 'parent_name', 'phone', 'email', 'grade_level', 'major_id', 'note'] as $field) {
        $response->assertSee('name="'.$field.'"', false);
        $response->assertSee('data-error-for="'.$field.'"', false);
    }

    $response->assertSee('شبکه و نرم‌افزار رایانه');
    $response->assertSee('مکاترونیک');
    $response->assertSee('دهم');
    $response->assertSee('یازدهم');
    $response->assertSee('دوازدهم');
});

test('the contact page shows the school information and the message form', function () {
    $response = $this->get(route('contact.index'));

    $response->assertOk();
    $response->assertSee('تماس با ما');
    $response->assertSee(config('school.address'));
    $response->assertSee('tel:'.config('school.manager_phone'), false);
    $response->assertSee('tel:'.config('school.deputy_phone'), false);
    $response->assertSee(config('school.manager_name'));
    $response->assertSee(config('school.deputy_name'));
    $response->assertSee('هنوز در وب‌سایت منتشر نشده است', false);
    $response->assertSee('data-app-form', false);
    $response->assertSee('action="'.route('contact.store', [], false).'"', false);

    foreach (['name', 'phone', 'email', 'subject', 'message'] as $field) {
        $response->assertSee('name="'.$field.'"', false);
        $response->assertSee('data-error-for="'.$field.'"', false);
    }

    $response->assertSee(route('admission.index'), false);
});

test('every public page of this lane shows the name of the school', function (string $url) {
    $this->get($url)
        ->assertOk()
        ->assertSee('هنرستان جوار شرکت پالایش نفت بندرعباس');
})->with([
    'majors' => fn () => route('majors.index'),
    'major' => fn () => route('majors.show', 'network-and-software'),
    'education' => fn () => route('education'),
    'gallery' => fn () => route('gallery'),
    'news' => fn () => route('news.index'),
    'article' => fn () => route('news.show', 'public-content-default-slug'),
    'admission' => fn () => route('admission.index'),
    'contact' => fn () => route('contact.index'),
]);

test('the two school facts are announced at most twice per page', function (string $url) {
    $html = $this->get($url)->assertOk()->getContent();

    /* The shared header and footer are not part of the page copy. */
    $content = preg_replace('/<header\b.*?<\/header>/su', '', $html);
    $content = preg_replace('/<footer\b.*?<\/footer>/su', '', $content);

    $majorsMentions = preg_match_all('/هر دو رشته|دو رشته|۲ رشته|2 رشته|رشته‌های فعال/u', $content);
    $gradesMentions = preg_match_all('/سه پایه|۳ پایه|پایه‌های تحصیلی|دهم تا دوازدهم/u', $content);

    expect($majorsMentions)->toBeLessThanOrEqual(2);
    expect($gradesMentions)->toBeLessThanOrEqual(2);
})->with([
    'majors' => fn () => route('majors.index'),
    'major' => fn () => route('majors.show', 'network-and-software'),
    'education' => fn () => route('education'),
    'gallery' => fn () => route('gallery'),
    'news' => fn () => route('news.index'),
    'admission' => fn () => route('admission.index'),
    'contact' => fn () => route('contact.index'),
]);

test('the weekly hours unit is only used as a column header', function (string $url) {
    $html = $this->get($url)->assertOk()->getContent();

    $tables = substr_count($html, '<table');
    $unit = substr_count($html, 'ساعت در هفته');

    /* One unit per table — the column header — and nowhere else. */
    expect($unit)->toBe($tables);
    expect($unit)->toBeLessThanOrEqual(3);
})->with([
    'education' => fn () => route('education'),
    'major' => fn () => route('majors.show', 'network-and-software'),
    'other major' => fn () => route('majors.show', 'mechatronics'),
]);

test('the education courses are grouped by major instead of repeating the name in every row', function () {
    $html = $this->get(route('education'))->assertOk()->getContent();

    $courseRows = substr_count($html, '<td');
    $groupHeaders = preg_match_all('/<th[^>]*scope="rowgroup"/u', $html);
    $majorNames = substr_count($html, 'شبکه و نرم‌افزار رایانه');

    /* Every grade block opens a group per major. */
    expect($groupHeaders)->toBeGreaterThan(0);

    /* The name of the major is not repeated in the rows: it appears once per
       group, not once per course. */
    expect($majorNames)->toBeLessThanOrEqual(6);
    expect($majorNames)->toBeLessThan($courseRows);
});

test('the repeated labels are cut from the majors and the education page', function () {
    $majors = $this->get(route('majors.index'))->assertOk()->getContent();

    /* The eyebrow repeated the section kicker. */
    expect($majors)->not->toContain('رشته‌های فعال هنرستان');
    /* The hero tile repeated the two major cards right below it. */
    expect($majors)->not->toContain('۲ رشته');

    $education = $this->get(route('education'))->assertOk()->getContent();

    /* The per-grade counters and the table captions repeated the fact. */
    expect($education)->not->toContain('درس در دو رشته');
    expect($education)->not->toContain('در دو رشتهٔ هنرستان');
    expect($education)->not->toContain('۲ رشته');
    expect($education)->not->toContain('۳ پایه');
});

test('the contact form shows the flash message when it is submitted without JavaScript', function () {
    $response = $this->followingRedirects()
        ->from(route('contact.index'))
        ->post(route('contact.store'), [
            'name' => 'زهرا کریمی',
            'phone' => '09121234567',
            'message' => 'پیام آزمایشی برای بررسی مسیر بدون جاوااسکریپت.',
        ]);

    $response->assertOk();
    $response->assertSee('data-flash-success', false);
    $response->assertSee('پیام شما با موفقیت ثبت شد');
});

test('the error views explain the situation and link to the contact page', function () {
    $notFound = view('errors.404', [
        'exception' => new RuntimeException('مطلب مورد نظر یافت نشد یا دیگر در دسترس نیست.'),
    ])->render();

    expect($notFound)
        ->toContain('مطلب مورد نظر یافت نشد')
        ->toContain(route('contact.index'));

    $serverError = view('errors.500', [
        'exception' => new RuntimeException('خطای داخلی سرور'),
    ])->render();

    expect($serverError)
        ->toContain('نمایش این صفحه با مشکل روبه‌رو شد')
        ->toContain(route('contact.index'));

    $expired = view('errors.419', [
        'exception' => new RuntimeException('نشست منقضی شده است.'),
    ])->render();

    expect($expired)
        ->toContain('نشست شما منقضی شده است')
        ->toContain('تلاش دوباره')
        ->toContain('صفحهٔ اصلی');

    /* An expired session is a self-service error: the page body itself offers
       no contact link. The shared chrome — header, fallback navigation, drawer
       and footer — is not page copy and always carries that link, so only the
       section the error page renders is inspected. */
    preg_match('/<section\b.*?<\/section>/su', $expired, $expiredBody);

    expect($expiredBody[0] ?? '')->not->toContain(route('contact.index'));

    /* There is no login on this public website, so the 419 view must not
       offer a "sign in again" action. */
    expect($expired)->not->toContain('ورود دوباره');
});
