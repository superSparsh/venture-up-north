<?php

use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\ContributorController as AdminContributorController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\VentureMagazineController;
use App\Http\Controllers\Admin\ExperiencesController;
use App\Http\Controllers\Admin\ExploreTileController;
use App\Http\Controllers\Admin\IndulgeLinkController;
use App\Http\Controllers\Admin\SitePopupController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TermsConditionController;
use App\Http\Controllers\Admin\ThingsToDoCategoryController;
use App\Http\Controllers\Admin\ThingsToDoItemController;
use App\Http\Controllers\Admin\TourTilesController;
use App\Http\Controllers\Admin\TownController;
use App\Http\Controllers\Admin\VentureAdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Frontend\CollectionController as FrontendCollectionController;
use App\Http\Controllers\Frontend\Contributor\EventsController;
use App\Http\Controllers\Frontend\Contributor\VentureMagazineController as ContributorVentureMagazineController;
use App\Http\Controllers\Frontend\ContributorController;
use App\Http\Controllers\Frontend\EventController as FrontendEventController;
use App\Http\Controllers\Frontend\ExperienceController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\RezdyBookingController as FrontendRezdyBookingController;
use App\Http\Controllers\Frontend\TagController as FrontendTagController;
use App\Http\Controllers\Frontend\ThingsToDoCategoryController as FrontendThingsToDoCategoryController;
use App\Http\Controllers\Frontend\ThingsToDoItemController as FrontendThingsToDoItemController;
use App\Http\Controllers\Frontend\TourTileController;
use App\Http\Controllers\Frontend\TownController as FrontendTownController;
use App\Http\Controllers\Frontend\VentureMagazineController as FrontendVentureMagazineController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VentureController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Messages\MailMessage;

Route::get('/notify-test', function () {
    $testNotification = new class extends \Illuminate\Notifications\Notification {
        public function via($notifiable)
        {
            return ['mail'];
        }

        public function toMail($notifiable)
        {
            return (new MailMessage)
                ->subject('Venture Up North — Test Notification')
                ->greeting('Hello 👋')
                ->line('This is a test email from Venture Up North.')
                ->line('If you are reading this, the SMTP configuration is working correctly.')
                ->action('Visit Website', url('/'))
                ->line('Thank you for testing!');
        }
    };

    // Change this to your email or use ADMIN_NOTIFY_LIST from .env
    Notification::route('mail', 'thakursparsh.st@gmail.com')
        ->notify($testNotification);

    return '✅ Test notification sent. Check your inbox.';
});

Route::get('/my-venture', function () {
    return Inertia::render('Frontend/Venture/MyVenture', [
        'seo' => [
            'title' => 'Venture Up North',
            'description' => "Don't Just Plan a Trip. Plan a Venture.",
            'image' => asset('/public/images/My-Venture.jpeg'),
            'canonical' => url()->current(),
            'robots' => 'index, follow', // keep private for now
            'type' => 'website',
        ],
    ]);
})->name('venture.my');

Route::get('/venture/share', function () {
    // This page reads ?i=... in the client and imports into local storage.
    return Inertia::render('Frontend/Venture/ImportShare', [
        'seo' => [
            'title' => 'Shared Venture',
            'description' => 'A shared Venture itinerary.',
            'image' => asset('/public/images/venture.png'),
            'canonical' => url()->current(),
            'robots' => 'index, follow',
            'type' => 'website',
        ],
    ]);
})->name('venture.share');

Route::get('/', [HomeController::class, 'index'])->name('home');

//Town Routes
Route::get('/town/{slug}',  [FrontendTownController::class, 'showTown'])->name('show.town');

//Tour Routes
Route::get('/tour/{slug}', [TourTileController::class, 'showTour'])->name('tours.show');
Route::get('/tours', [TourTileController::class, 'tours'])->name('tours.display');

//Experience Routes
Route::get('/experience/{slug}', function ($slug) {
    return redirect()->route('booking.experience', ['slug' => $slug], 301);
});
//Magazine Routes
Route::get('/magazine/{slug}',  [FrontendVentureMagazineController::class, 'showMagazine'])->name('show.magazine');
Route::get('/magazines/ours', [FrontendVentureMagazineController::class, 'ours'])->name('ours.magazine');
Route::get('/magazines/community', [FrontendVentureMagazineController::class, 'community'])->name('community.magazine');

//Rezdy Url
Route::get('/venture/book/{slug}', [FrontendRezdyBookingController::class, 'show'])->name('booking.show');
Route::get('/experience/book/{slug}', [FrontendRezdyBookingController::class, 'experience'])->name('booking.experience');
Route::get('/tours/book/{slug}', [FrontendRezdyBookingController::class, 'tourTiles'])->name('booking.tour');

//Category Routes
Route::get('/explore/{slug}',  [FrontendThingsToDoCategoryController::class, 'showCategory'])->name('show.category');

//Category Listing Routes
Route::get('/explore/{category_slug}/{slug}', [FrontendThingsToDoItemController::class, 'showListing'])->name('category.listing.show');

//Collections Route
Route::get('/collection/{slug}', [FrontendCollectionController::class, 'showCollection'])->name('show.collection');
Route::get('/collections', [FrontendCollectionController::class, 'collections'])->name('collections.display');

//Event Route
Route::get('/event/{slug}', [FrontendEventController::class, 'showEvent'])->name('event.show');
Route::get('/events', [FrontendEventController::class, 'events'])->name('events.display');

//Contact Us
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');


// Public create from client (guest allowed)
Route::post('/ventures', [VentureController::class, 'store'])->name('ventures.store');

// Public view
Route::get('/ventures/{slug}', [VentureController::class, 'show'])->name('ventures.show');
// Owner edit (guest or logged in)
Route::get('/ventures/{slug}/edit', [VentureController::class, 'editVenture'])
    ->name('ventures.edit');
// Persist edits
Route::post('/ventures/{slug}', [VentureController::class, 'update'])->name('ventures.update');
// Lists
Route::get('/venture/your', [VentureController::class, 'your'])->name('ventures.your');
Route::get('/venture/ours', [VentureController::class, 'ours'])->name('ventures.ours');
Route::get('/venture/community', [VentureController::class, 'community'])->name('ventures.community');


Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create();

    // Static pages (no lastmod needed unless you want to set a static date)
    $sitemap->add(Url::create('/'));
    $sitemap->add(Url::create('/contact'));
    $sitemap->add(Url::create('/events'));
    $sitemap->add(Url::create('/collections'));
    $sitemap->add(Url::create('/tours'));

    // Towns
    \App\Models\Town::all()->each(function ($town) use ($sitemap) {
        $sitemap->add(Url::create("/town/{$town->slug}")->setLastModificationDate($town->updated_at));
        $sitemap->add(Url::create("/venture/book/{$town->slug}")->setLastModificationDate($town->updated_at));
    });

    // Experiences
    \App\Models\Experience::all()->each(function ($experience) use ($sitemap) {
        $sitemap->add(Url::create("/experience/{$experience->slug}")->setLastModificationDate($experience->updated_at));
        $sitemap->add(Url::create("/experience/book/{$experience->slug}")->setLastModificationDate($experience->updated_at));
    });

    // Magazine Posts
    \App\Models\VentureMagazine::all()->each(function ($post) use ($sitemap) {
        $sitemap->add(Url::create("/magazine/{$post->slug}")->setLastModificationDate($post->updated_at));
    });

    // Tour Tiles
    \App\Models\TourTile::all()->each(function ($post) use ($sitemap) {
        $sitemap->add(Url::create("/tours/book/{$post->slug}")->setLastModificationDate($post->updated_at));
    });

    // Events (only upcoming)
    \App\Models\Event::upcoming()->get()->each(function ($event) use ($sitemap) {
        $sitemap->add(Url::create("/event/{$event->slug}")->setLastModificationDate($event->updated_at));
    });

    // Content Categories
    \App\Models\ThingsToDoCategory::all()->each(function ($content) use ($sitemap) {
        $sitemap->add(Url::create("/explore/{$content->slug}")->setLastModificationDate($content->updated_at));
    });

    // Content Items (Things to Do)
    \App\Models\ThingsToDoItem::with('category:id,slug')->get()->each(function ($item) use ($sitemap) {
        $categorySlug = $item->category?->slug;
        if ($categorySlug) {
            $sitemap->add(
                Url::create("/explore/{$categorySlug}/{$item->slug}")
                    ->setLastModificationDate($item->updated_at)
            );
        }
    });

    // Collections
    \App\Models\Collection::all()->each(function ($collection) use ($sitemap) {
        $sitemap->add(Url::create("/collection/{$collection->slug}")->setLastModificationDate($collection->updated_at));
    });

    return $sitemap->toResponse(request());
});

Route::get('/sitemap', [HomeController::class, 'sitemap'])->name('sitemap.page');
Route::get('/upcoming-features', [HomeController::class, 'upcomingFeatures'])->name('upcoming.features');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//Contributor Routes
Route::middleware(['auth', 'verified', 'contributor'])->group(function () {
    //Profile Routes
    Route::get('/contributor/profile', [ProfileController::class, 'editContributor'])->name('contributor.profile.edit');
    Route::post('/contributor/profile', [ProfileController::class, 'updateContributor'])->name('contributor.profile.update');

    //Dashboard
    Route::delete('/contributor/profile', [ProfileController::class, 'destroyContributor'])->name('contributor.profile.destroy');
    Route::get('/dashboard/contributor/{slug}', [ContributorController::class, 'dashboard'])
        ->name('contributors.dashboard');

    //Magazines Route
    Route::get('/contributor/magazines', [ContributorVentureMagazineController::class, 'index'])
        ->name('contributor.magazines.index');
    Route::get('/contributor/new-magazine', [ContributorVentureMagazineController::class, 'create'])->name('contributor.magazines.create');
    Route::post('/contributor/new-magazine', [ContributorVentureMagazineController::class, 'store'])->name('contributor.magazines.store');
    Route::get('/contributor/edit-magazine/{ventureMagazine}', [ContributorVentureMagazineController::class, 'edit'])->name('contributor.magazines.edit');
    Route::post('/contributor/update-magazine/{ventureMagazine}', [ContributorVentureMagazineController::class, 'update'])->name('contributor.magazines.update');
    Route::delete('/contributor/destroy/magazine/{ventureMagazine}', [ContributorVentureMagazineController::class, 'destroy'])
        ->name('contributor.magazines.destroy');
    //Events Routes
    Route::get('/contributor/events', [EventsController::class, 'index'])
        ->name('contributor.events.index');
    Route::get('/contributor/event', [EventsController::class, 'create'])->name('contributor.events.create');
    Route::post('/contributor/event', [EventsController::class, 'store'])->name('contributor.events.store');
    Route::get('/contributor/edit-event/{event}', [EventsController::class, 'edit'])->name('contributor.events.edit');
    Route::post('/contributor/update-event/{event}', [EventsController::class, 'update'])->name('contributor.events.update');
    Route::delete('/contributor/destroy/event/{event}', [EventsController::class, 'destroy'])
        ->name('contributor.events.destroy');
});



//Team Member Routes
Route::middleware(['auth', 'verified', 'teamMember'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Magazine Route
    Route::get('/my-magazines', [FrontendVentureMagazineController::class, 'index'])->name('user.magazines.index');
    Route::get('/new-magazine', [FrontendVentureMagazineController::class, 'create'])->name('user.magazines.create');
    Route::post('/new-magazine', [FrontendVentureMagazineController::class, 'store'])->name('user.magazines.store');
    Route::get('/edit-magazine/{ventureMagazine}', [FrontendVentureMagazineController::class, 'edit'])->name('user.magazines.edit');
    Route::post('/update-magazine/{ventureMagazine}', [FrontendVentureMagazineController::class, 'update'])->name('user.magazines.update');
    Route::delete('/destroy/{ventureMagazine}', [FrontendVentureMagazineController::class, 'destroy'])
        ->name('user.magazines.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => Inertia::render('Admin/Dashboard'))->name('dashboard');
    Route::get('/home-sections', fn() => Inertia::render('Admin/HomeSections/Index'))->name('home-sections.index');

    //HomeSections Route
    Route::resource('home-sections', HomeSectionController::class)->except(['show']);
    Route::put('/admin/home-sections/{homeSection}/toggle-status', [HomeSectionController::class, 'toggleStatus'])->name('home-sections.toggle-status');

    //Team Member Route
    Route::resource('team-members', TeamMemberController::class);
    Route::put('team-members/{teamMember}/toggle-status', [TeamMemberController::class, 'toggleStatus'])->name('team-members.toggle-status');

    //Venture Magazine Route
    Route::resource('venture-magazines', VentureMagazineController::class);
    Route::put('venture-magazines/{ventureMagazine}/toggle-status', [VentureMagazineController::class, 'toggleFeatured'])->name('magazines.toggle-status');

    //Town Route
    Route::resource('towns', TownController::class);
    Route::put('towns/{town}/toggle-status', [TownController::class, 'toggleStatus'])->name('towns.toggle-status');

    //Experience Route
    Route::resource('experiences', ExperiencesController::class);
    Route::put('experiences/{experience}/toggle-status', [ExperiencesController::class, 'toggleStatus'])->name('experiences.toggle-status');

    //TourTiles Route
    Route::resource('tour-tiles', TourTilesController::class);
    Route::put('tour-tiles/{tourTile}/toggle-status', [TourTilesController::class, 'toggleStatus'])->name('tour-tiles.toggle-status');
    Route::get('tour/settings', [TourTilesController::class, 'editSetting'])->name('tour.settings');
    Route::post('tour/settings', [TourTilesController::class, 'updateSetting'])->name('tour.settings.update');

    //Contact Route
    Route::get('/contact', [ContactController::class, 'showContacts'])->name('contacts.index');
    Route::delete('/destroy/{contact}', [ContactController::class, 'deleteContact'])->name('contacts.destroy');

    //Site Settings
    Route::get('/site/settings', [SiteSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/footer', [SiteSettingsController::class, 'updateFooter'])->name('footer.update');
    Route::post('/admin/site-settings/seo', [SiteSettingsController::class, 'updateSeo'])->name('site-seo.update');

    //Things To do Route
    Route::resource('categories', ThingsToDoCategoryController::class);
    Route::put('categories/{category}/toggle-status', [ThingsToDoCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    //Things To Do Item Route
    Route::resource('category-listings', ThingsToDoItemController::class);
    Route::put('category-listings/{listing}/toggle-status', [ThingsToDoItemController::class, 'toggleStatus'])->name('category-listings.toggle-status');

    //Tags Route
    Route::resource('tags', TagController::class);
    Route::put('tags/{tag}/toggle-status', [TagController::class, 'toggleStatus'])->name('tags.toggle-status');

    //Collections Route
    Route::resource('collections', CollectionController::class);
    Route::put('collections/{collection}/toggle-status', [CollectionController::class, 'toggleStatus'])->name('collections.toggle-status');
    Route::get('collection/settings', [CollectionController::class, 'editSetting'])->name('collections.settings');
    Route::post('collection/settings', [CollectionController::class, 'updateSetting'])->name('collections.settings.update');

    //Events Route
    Route::resource('events', EventController::class);
    Route::put('events/{event}/toggle-status', [EventController::class, 'toggleStatus'])->name('events.toggle-status');
    Route::get('event/settings', [EventController::class, 'editSetting'])->name('events.settings');
    Route::post('event/settings', [EventController::class, 'updateSetting'])->name('events.settings.update');

    //Explore Tiles
    Route::resource('explore-tiles', ExploreTileController::class);
    Route::put('explore-tiles/{tile}/toggle-status', [ExploreTileController::class, 'toggleStatus'])->name('explore-tiles.toggle-status');

    //Explore Indulge
    Route::resource('indulge-links', IndulgeLinkController::class);
    Route::put('indulge-links/{link}/toggle-status', [IndulgeLinkController::class, 'toggleStatus'])->name('indulge-links.toggle-status');

    //Contributor Profile and Review
    Route::resource('contributors', AdminContributorController::class);
    Route::put('contributors/{contributor}/toggle-status', [AdminContributorController::class, 'toggleStatus'])->name('contributors.toggle-status');
    //Magazine Contributors
    Route::get('/contributor/venture-magazines', [VentureMagazineController::class, 'contributors'])
        ->name('venture-magazines.contributors');
    Route::get('/contributor/venture-magazine/review/{slug}', [VentureMagazineController::class, 'showReviewMagazine'])->name('magazines.review');
    Route::post('/approve/venture-magazines/{magazine}', [VentureMagazineController::class, 'approve'])
        ->name('venture-magazines.approve');
    Route::post('/reject/venture-magazines/{magazine}', [VentureMagazineController::class, 'reject'])
        ->name('venture-magazines.reject');
    //Event Contributors
    Route::get('/contributor/events', [EventController::class, 'contributors'])
        ->name('events.contributors');
    Route::get('/contributor/event/review/{slug}', [EventController::class, 'showReviewEvent'])->name('events.review');
    Route::post('/approve/event/{event}', [EventController::class, 'approve'])
        ->name('events.approve');
    Route::post('/reject/event/{event}', [EventController::class, 'reject'])
        ->name('events.reject');

    //Terms & Condition
    Route::get('/terms-conditions', [TermsConditionController::class, 'edit'])
        ->name('terms.edit');
    Route::put('/terms-conditions', [TermsConditionController::class, 'update'])
        ->name('terms.update');
    Route::get('/event-terms-conditions', [TermsConditionController::class, 'editEvent'])
        ->name('event.terms.edit');
    Route::put('/event-terms-conditions', [TermsConditionController::class, 'updateEvent'])
        ->name('event.terms.update');

    //Popup Controller
    Route::get('/popups', [SitePopupController::class, 'edit'])->name('popups.edit');
    Route::put('/popups', [SitePopupController::class, 'update'])->name('popups.update');

    //My Venture 
    Route::get('/ventures',                [VentureAdminController::class, 'index'])->name('ventures.index');
    Route::get('/ventures/ours',           [VentureAdminController::class, 'ourIndex'])->name('ventures.ours.index');
    Route::get('/ventures/{venture}/edit', [VentureAdminController::class, 'edit'])->name('ventures.edit');
    Route::put('/ventures/{venture}',      [VentureAdminController::class, 'update'])->name('ventures.update');
    Route::delete('/admin/ventures/{venture}', [VentureAdminController::class, 'destroy'])
        ->name('ventures.destroy');
});

require __DIR__ . '/auth.php';
