<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\AuctionRequest;
use App\Models\Bid;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('auction_user_favorites')->truncate();
        if (Schema::hasTable('auction_requests')) {
            AuctionRequest::truncate();
        }
        Bid::truncate();
        Auction::truncate();
        Category::truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $demoUser = User::factory()->create([
            'name' => 'Aarav Mehta',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $sellerNames = [
            'Maya Kapoor', 'Rohan Shah', 'Anika Rao', 'Kabir Malhotra', 'Sophia Bennett',
            'Ethan Walker', 'Nisha Iyer', 'Liam Carter', 'Priya Nair', 'Arjun Sethi',
            'Elena Brooks', 'Marcus Chen',
        ];

        $users = collect($sellerNames)->map(fn ($name) => User::factory()->create([
            'name' => $name,
            'email' => Str::slug($name). '@auctionpro.test',
            'password' => Hash::make('password'),
        ]))->push($demoUser);

        $categories = collect([
            'Electronics', 'Watches', 'Sneakers', 'Cars', 'Gaming', 'Luxury', 'Cameras', 'Collectibles',
        ])->mapWithKeys(fn ($name) => [$name => Category::create(['name' => $name, 'slug' => Str::slug($name)])]);

        $descriptions = [
            'Carefully inspected and photographed for this auction. The item is fully functional, includes the accessories listed in the specification panel, and ships with secure packaging after payment confirmation.',
            'A collector-grade listing from a verified seller. Expect light signs of handling only where noted, clean presentation, and a transparent bidding history throughout the auction.',
            'Premium marketplace listing with clear ownership history, realistic reserve pricing, and fast insured delivery. Ideal for buyers who want a ready-to-use item without retail markup.',
        ];

        $listings = [
            ['Apple iPhone 15 Pro Max - 256GB Natural Titanium', 'Electronics', 'iphone-15-pro.jpg', 740, 25, 'Excellent', ['Storage' => '256GB', 'Battery Health' => '96%', 'Network' => 'Unlocked', 'Includes' => 'Box and USB-C cable']],
            ['MacBook Pro 14-inch M3 Pro - Space Black', 'Electronics', 'macbook-pro.jpg', 1450, 50, 'Like New', ['Processor' => 'M3 Pro', 'Memory' => '18GB', 'Storage' => '512GB SSD', 'Cycle Count' => '42']],
            ['Sony PlayStation 5 Digital Edition Bundle', 'Gaming', 'gaming-console.jpg', 330, 15, 'Excellent', ['Storage' => '825GB', 'Controllers' => '2 DualSense', 'Games' => '3 digital titles', 'Warranty' => 'Seller warranty']],
            ['Rolex Submariner Style Automatic Diver Watch', 'Watches', 'rolex-watch.jpg', 2650, 100, 'Collector Grade', ['Movement' => 'Automatic', 'Case' => 'Stainless steel', 'Crystal' => 'Sapphire', 'Box' => 'Included']],
            ['Nike Air Jordan Retro High - University Blue', 'Sneakers', 'sneakers.jpg', 210, 10, 'Deadstock', ['Size' => 'US 10', 'Condition' => 'Unworn', 'Box' => 'Original', 'Authentication' => 'Verified']],
            ['Porsche 911 Carrera Scale Collector Model', 'Cars', 'sports-car.jpg', 1200, 50, 'Showroom', ['Year' => '2021', 'Documentation' => 'Available', 'Inspection' => 'Completed', 'Delivery' => 'Insured transport']],
            ['Canon EOS R Mirrorless Camera Kit', 'Cameras', 'camera.jpg', 890, 25, 'Excellent', ['Sensor' => '30.3MP', 'Lens' => 'RF 24-105mm', 'Shutter Count' => '8,200', 'Includes' => 'Bag and charger']],
            ['Vintage Film Camera with Prime Lens', 'Collectibles', 'vintage-camera.jpg', 180, 10, 'Good', ['Format' => '35mm', 'Lens' => '50mm prime', 'Meter' => 'Working', 'Case' => 'Leather case']],
            ['Sony WH-1000XM5 Noise Cancelling Headphones', 'Electronics', 'headphones.jpg', 190, 10, 'Excellent', ['Color' => 'Black', 'Battery' => '30 hours', 'Includes' => 'Case', 'Connectivity' => 'Bluetooth']],
            ['Luxury Leather Crossbody Bag - Black', 'Luxury', 'luxury-bag.jpg', 420, 20, 'Excellent', ['Material' => 'Full-grain leather', 'Color' => 'Black', 'Hardware' => 'Gold tone', 'Dust Bag' => 'Included']],
            ['Technics Inspired Direct Drive Turntable', 'Collectibles', 'turntable.jpg', 310, 15, 'Very Good', ['Drive' => 'Direct', 'Cartridge' => 'Included', 'Cover' => 'Clear acrylic', 'Tested' => 'Yes']],
            ['DJI Mini Drone Fly More Kit', 'Electronics', 'drone.jpg', 460, 20, 'Excellent', ['Flight Time' => '31 min', 'Batteries' => '3', 'Camera' => '4K', 'Case' => 'Included']],
        ];

        for ($i = 0; $i < 30; $i++) {
            $item = $listings[$i % count($listings)];
            [$title, $category, $image, $starting, $increment, $condition, $specs] = $item;
            $statusOffset = $i % 6;
            $startsAt = match (true) {
                $statusOffset === 0 => now()->addDays(random_int(1, 4)),
                $statusOffset === 1 => now()->subDays(random_int(2, 6)),
                default => now()->subHours(random_int(2, 20)),
            };
            $endsAt = $statusOffset === 1
                ? now()->subHours(random_int(2, 12))
                : (clone $startsAt)->modify('+'.random_int(12, 120).' hours');

            $auction = Auction::create([
                'user_id' => $users->random()->id,
                'category_id' => $categories[$category]->id,
                'title' => $i < count($listings) ? $title : $title.' - Lot #'.($i + 101),
                'description' => $descriptions[$i % count($descriptions)].' This lot is popular with buyers tracking '.$category.' auctions and has been priced to encourage competitive bidding.',
                'starting_price' => $starting + random_int(0, 180),
                'min_increment' => $increment,
                'image_path' => 'auctions/'.$image,
                'gallery_images' => ['auctions/'.$image, 'auctions/'.$listings[($i + 3) % count($listings)][2], 'auctions/'.$listings[($i + 5) % count($listings)][2]],
                'condition' => $condition,
                'specifications' => $specs,
                'shipping_details' => random_int(0, 1) ? 'Insured shipping within 2-4 business days' : 'Local pickup or secure courier available',
                'approval_status' => 'approved',
                'is_featured' => $i < 6,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            if ($auction->starts_at->lte(now())) {
                $amount = (float) $auction->starting_price;
                foreach ($users->where('id', '!=', $auction->user_id)->random(random_int(3, 7)) as $user) {
                    $amount += (float) $auction->min_increment + random_int(5, 80);
                    Bid::create([
                        'auction_id' => $auction->id,
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'created_at' => now()->subMinutes(random_int(5, 1800)),
                        'updated_at' => now()->subMinutes(random_int(5, 1800)),
                    ]);
                }

                $auction->update([
                    'current_price' => $amount,
                    'closed_at' => $auction->ends_at->lte(now()) ? $auction->ends_at : null,
                    'winner_id' => $auction->ends_at->lte(now()) ? $auction->highestBid?->user_id : null,
                ]);
            }
        }

        foreach (array_slice($listings, 0, 9) as $index => $item) {
            [$title, $category, $image, $starting, $increment, $condition, $specs] = $item;
            AuctionRequest::create([
                'seller_id' => $users->random()->id,
                'category_id' => $categories[$category]->id,
                'title' => $title.' - Seller Submission',
                'description' => 'Seller-submitted listing awaiting marketplace moderation. Photos, condition details, and price expectations are ready for admin review.',
                'starting_price' => $starting,
                'reserve_price' => $starting + random_int(80, 400),
                'min_increment' => $increment,
                'image_path' => 'auctions/'.$image,
                'gallery_images' => ['auctions/'.$image],
                'condition' => $condition,
                'specifications' => $specs,
                'shipping_details' => 'Seller can ship with insured courier after approval.',
                'starts_at' => now()->addHours(random_int(4, 48)),
                'ends_at' => now()->addDays(random_int(4, 10)),
                'status' => $index < 5 ? 'pending' : ($index < 7 ? 'approved' : 'rejected'),
                'moderation_notes' => $index >= 7 ? 'Please upload clearer product photos and proof of ownership.' : null,
                'reviewed_at' => $index >= 5 ? now()->subHours(random_int(2, 24)) : null,
            ]);
        }

        // Add our 70 realistic auctions
        $this->call(SeventyAuctionsSeeder::class);
    }
}
