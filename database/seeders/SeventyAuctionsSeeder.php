<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeventyAuctionsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        $this->command->info('Fetching products from DummyJSON API to get realistic titles, descriptions, and photos...');
        
        $response = Http::timeout(30)->get('https://dummyjson.com/products?limit=194');
        if (!$response->successful()) {
            $this->command->error('Failed to fetch from DummyJSON');
            return;
        }

        $allProducts = collect($response->json('products'));
        
        // Filter out boring categories to favor the ones requested
        $allowedCategories = [
            'smartphones', 'laptops', 'mens-watches', 'womens-watches', 
            'mens-shoes', 'womens-shoes', 'sunglasses', 'automotive', 
            'motorcycle', 'lighting', 'furniture', 'womens-bags', 'womens-jewellery',
            'sports-accessories', 'tablets', 'mobile-accessories'
        ];

        $products = $allProducts->filter(function ($product) use ($allowedCategories) {
            return in_array($product['category'] ?? '', $allowedCategories);
        })->take(70)->values();
        
        if ($products->count() < 70) {
            // Pad with others if we don't have exactly 70
            $needed = 70 - $products->count();
            $extras = $allProducts->filter(function ($product) use ($allowedCategories) {
                return !in_array($product['category'] ?? '', $allowedCategories);
            })->take($needed)->values();
            $products = $products->concat($extras);
        }

        $this->command->info('Processing and downloading images for 70 auctions... (this may take a minute)');

        // Map categories to standard names
        $categoryMap = [
            'smartphones' => 'Smartphones',
            'laptops' => 'Laptops',
            'tablets' => 'Tablets',
            'mobile-accessories' => 'Accessories',
            'mens-watches' => 'Watches',
            'womens-watches' => 'Watches',
            'mens-shoes' => 'Sneakers',
            'womens-shoes' => 'Shoes',
            'sunglasses' => 'Luxury Accessories',
            'womens-bags' => 'Luxury Accessories',
            'womens-jewellery' => 'Jewelry',
            'automotive' => 'Cars',
            'motorcycle' => 'Motorcycles',
            'lighting' => 'Home Tech',
            'furniture' => 'Home Decor',
            'sports-accessories' => 'Collectibles',
        ];

        Storage::disk('public')->makeDirectory('auctions');

        foreach ($products as $index => $product) {
            $catName = $categoryMap[$product['category'] ?? ''] ?? ucwords(str_replace('-', ' ', $product['category'] ?? 'Other'));
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($catName)],
                ['name' => $catName]
            );

            // Download real product image
            $imageUrl = $product['thumbnail'] ?? ($product['images'][0] ?? null);
            $imagePath = null;
            
            if ($imageUrl) {
                try {
                    $imageContents = Http::timeout(10)->get($imageUrl)->body();
                    $filename = 'auctions/' . Str::random(40) . '.jpg';
                    Storage::disk('public')->put($filename, $imageContents);
                    $imagePath = $filename;
                } catch (\Exception $e) {
                    $imagePath = null; // Continue even if one image fails
                }
            }

            // Status Offset: 0 = Upcoming, 1 = Active, 2 = Closed
            $statusOffset = $index % 3; 
            
            $startsAt = match ($statusOffset) {
                0 => now()->addDays(random_int(1, 5)),
                1 => now()->subDays(random_int(1, 5)),
                2 => now()->subDays(random_int(5, 10)),
            };
            
            $endsAt = match ($statusOffset) {
                0 => (clone $startsAt)->addDays(random_int(3, 7)),
                1 => now()->addDays(random_int(1, 7)),
                2 => now()->subDays(random_int(1, 4)),
            };

            $startingPrice = max(10, round(($product['price'] ?? random_int(50, 500)) * 0.7));
            $increment = max(1, round($startingPrice * 0.05));

            $specs = [];
            if (!empty($product['brand'])) $specs['Brand'] = $product['brand'];
            if (!empty($product['weight'])) $specs['Weight'] = $product['weight'] . 'g';
            if (!empty($product['sku'])) $specs['SKU'] = $product['sku'];

            $auction = Auction::create([
                'user_id' => $users->random()->id,
                'category_id' => $category->id,
                'title' => $product['title'] ?? 'Premium Item ' . $index,
                'description' => $product['description'] ?? 'An amazing auction item ready for bidding.',
                'starting_price' => $startingPrice,
                'min_increment' => $increment,
                'image_path' => $imagePath,
                'gallery_images' => $imagePath ? [$imagePath] : [],
                'condition' => collect(['New', 'Like New', 'Excellent', 'Good'])->random(),
                'specifications' => $specs,
                'shipping_details' => collect(['Free Shipping', 'Local Pickup', 'Calculated at checkout'])->random(),
                'approval_status' => 'approved',
                'is_featured' => random_int(0, 10) > 8,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            // Create realistic bidding history for active and closed
            if ($statusOffset > 0) {
                $amount = (float) $auction->starting_price;
                $numBids = random_int(3, 12);
                
                $bidUsers = $users->where('id', '!=', $auction->user_id);
                if ($bidUsers->count() > 0) {
                    $selectedUsers = $bidUsers->random(min($numBids, $bidUsers->count()));
                    foreach ($selectedUsers as $user) {
                        $amount += (float) $auction->min_increment + random_int(1, 50);
                        Bid::create([
                            'auction_id' => $auction->id,
                            'user_id' => $user->id,
                            'amount' => $amount,
                            'created_at' => (clone $startsAt)->addMinutes(random_int(10, 2000)),
                            'updated_at' => now(),
                        ]);
                    }
                }

                $auction->update([
                    'current_price' => $amount,
                    'closed_at' => $statusOffset === 2 ? $auction->ends_at : null,
                    'winner_id' => $statusOffset === 2 ? $auction->highestBid?->user_id : null,
                ]);
            }
        }

        $this->command->info('70 realistic auctions seeded successfully!');
    }
}
