<?php

namespace Database\Seeders;

use App\Models\Negotiation;
use App\Models\NegotiationMessage;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Farmers (one per farm name seen in the mock data) ──
        $greenValley = User::create([
            'name' => 'Ngozi Eze', 'email' => 'greenvalley@farmdirect.test',
            'password' => Hash::make('password'), 'role' => 'farmer',
            'farm_name' => 'Green Valley Farm', 'location' => 'Kuje, Abuja',
        ]);
        $gwagwalada = User::create([
            'name' => 'Musa Ibrahim', 'email' => 'gwagwalada@farmdirect.test',
            'password' => Hash::make('password'), 'role' => 'farmer',
            'farm_name' => 'Gwagwalada Farms', 'location' => 'Gwagwalada, Abuja',
        ]);
        $kujeHoney = User::create([
            'name' => 'Amina Yusuf', 'email' => 'kujehoney@farmdirect.test',
            'password' => Hash::make('password'), 'role' => 'farmer',
            'farm_name' => 'Kuje Honey Farm', 'location' => 'Kuje, Abuja',
        ]);
        $usmanFarms = User::create([
            'name' => 'Usman Bello', 'email' => 'usmanfarms@farmdirect.test',
            'password' => Hash::make('password'), 'role' => 'farmer',
            'farm_name' => 'Usman Farms', 'location' => 'Lugbe, Abuja',
        ]);
        $berryBliss = User::create([
            'name' => 'Grace Danladi', 'email' => 'berrybliss@farmdirect.test',
            'password' => Hash::make('password'), 'role' => 'farmer',
            'farm_name' => 'Berry Bliss Farm', 'location' => 'Jos, Plateau',
        ]);
        $sunnyAcres = User::create([
            'name' => 'Tunde Adebayo', 'email' => 'sunnyacres@farmdirect.test',
            'password' => Hash::make('password'), 'role' => 'farmer',
            'farm_name' => 'Sunny Acres', 'location' => 'Karu, Nasarawa',
        ]);

        // ── One demo customer ──
        $customer = User::create([
            'name' => 'Demilade', 'email' => 'demilade@farmdirect.test',
            'password' => Hash::make('password'), 'role' => 'customer',
        ]);

        // ── Products (matches src/lib/mock-data.ts exactly) ──
        $products = [
            ['slug' => 'garri', 'name' => 'Garri (Ijebu, Bulk)', 'category' => 'Pantry', 'price' => 450, 'unit' => 'module', 'farmer_id' => $greenValley->id, 'location' => 'Kuje, Abuja', 'rating' => 4.8, 'reviews_count' => 124, 'stock' => '24 modules available', 'badge' => 'Best Seller', 'description' => 'Stone-free Ijebu garri, sun-dried and sieved the traditional way.'],
            ['slug' => 'tomatoes', 'name' => 'Fresh Tomatoes', 'category' => 'Vegetables', 'price' => 2000, 'unit' => 'kg', 'farmer_id' => $gwagwalada->id, 'location' => 'Gwagwalada, Abuja', 'rating' => 4.9, 'reviews_count' => 96, 'stock' => '40 kg available', 'description' => 'Firm, deep-red field tomatoes picked at dawn.'],
            ['slug' => 'pure-honey', 'name' => 'Pure Honey', 'category' => 'Honey & Jams', 'price' => 1200, 'unit' => '500g', 'farmer_id' => $kujeHoney->id, 'location' => 'Kuje, Abuja', 'rating' => 5.0, 'reviews_count' => 58, 'stock' => '18 jars available', 'badge' => 'Top Rated', 'description' => 'Raw, unfiltered wildflower honey harvested from our own hives.'],
            ['slug' => 'smoked-catfish', 'name' => 'Smoked Catfish', 'category' => 'Protein', 'price' => 2500, 'unit' => '250g', 'farmer_id' => $usmanFarms->id, 'location' => 'Lugbe, Abuja', 'rating' => 4.7, 'reviews_count' => 42, 'stock' => '12 packs available', 'description' => 'Whole catfish smoked slowly over hardwood.'],
            ['slug' => 'strawberries', 'name' => 'Sweet Summer Strawberries', 'category' => 'Fruits', 'price' => 3000, 'unit' => 'kg', 'farmer_id' => $berryBliss->id, 'location' => 'Jos, Plateau', 'rating' => 4.9, 'reviews_count' => 71, 'stock' => '9 kg available', 'badge' => 'Just In', 'description' => 'Highland strawberries grown in the cool Jos plateau climate.'],
            ['slug' => 'sweet-potato', 'name' => 'Organic Sweet Potato', 'category' => 'Tubers', 'price' => 1250, 'unit' => 'kg', 'farmer_id' => $greenValley->id, 'location' => 'Kuje, Abuja', 'rating' => 4.8, 'reviews_count' => 63, 'stock' => '35 kg available', 'description' => 'Sweet, orange-fleshed potatoes grown without synthetic fertiliser.'],
            ['slug' => 'ugu-leaves', 'name' => 'Fresh Ugu Leaves (Bulk)', 'category' => 'Vegetables', 'price' => 550, 'unit' => 'bundle', 'farmer_id' => $greenValley->id, 'location' => 'Nyanya, Abuja', 'rating' => 4.7, 'reviews_count' => 88, 'stock' => '60 bundles available', 'description' => 'Fluted pumpkin leaves cut and bundled to order.'],
            ['slug' => 'habanero', 'name' => 'Habanero Peppers (Atarodo)', 'category' => 'Vegetables', 'price' => 620, 'unit' => 'cup', 'farmer_id' => $sunnyAcres->id, 'location' => 'Karu, Nasarawa', 'rating' => 4.6, 'reviews_count' => 54, 'stock' => '50 cups available', 'description' => 'Fiery red atarodo sorted by hand for size and ripeness.'],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }

        // ── Two seeded negotiations, matching mock-data.ts `negotiations` array ──
        $ugu = Product::where('slug', 'ugu-leaves')->first();
        $negotiation1 = Negotiation::create([
            'product_id' => $ugu->id, 'buyer_id' => $customer->id, 'farmer_id' => $ugu->farmer_id,
            'buyer_offer' => 500, 'farmer_ask' => 550, 'status' => 'Offer Sent',
        ]);
        NegotiationMessage::create(['negotiation_id' => $negotiation1->id, 'side' => 'buyer', 'text' => 'Good morning. Can I get 20 bundles at ₦500 each?']);
        NegotiationMessage::create(['negotiation_id' => $negotiation1->id, 'side' => 'farmer', 'text' => 'Morning! Let me check what we cut today and revert.']);

        $habanero = Product::where('slug', 'habanero')->first();
        $negotiation2 = Negotiation::create([
            'product_id' => $habanero->id, 'buyer_id' => $customer->id, 'farmer_id' => $habanero->farmer_id,
            'buyer_offer' => 560, 'farmer_ask' => 620, 'status' => 'Counter-Offer Received',
        ]);
        NegotiationMessage::create(['negotiation_id' => $negotiation2->id, 'side' => 'buyer', 'text' => "I'd like 30 cups at ₦560 per cup."]);
        NegotiationMessage::create(['negotiation_id' => $negotiation2->id, 'side' => 'farmer', 'text' => "I can't go that low for 30, but I can do ₦620 per cup."]);

        $this->command->info('Seeded 7 users, 8 products, and 2 negotiations matching the Lovable mock data.');
    }
}
