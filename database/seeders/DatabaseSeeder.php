<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            'baju' => 'Tees',
            'celana' => 'Bottoms',
            'hoodie' => 'Hoodies',
            'aksesoris' => 'Accessories',
            'topi' => 'Caps',
            'sandal' => 'Slippers',
        ];

        foreach ($categories as $slug => $name) {
            Category::updateOrCreate(['slug' => $slug], ['name' => $name]);
        }

        $articles = [
            'halloween' => [
                'name' => 'Halloween Drop',
                'description' => 'Edisi khusus Halloween — prints gelap, motif jack-o-lantern, dan vibe misterius.',
            ],
            'natal' => [
                'name' => 'Natal Collection',
                'description' => 'Koleksi Natal — warna hangat, print merry & bright, cocok buat hadiah.',
            ],
        ];

        $articleIds = [];
        foreach ($articles as $slug => $data) {
            $articleIds[$slug] = Article::updateOrCreate(['slug' => $slug], $data)->id;
        }

        User::updateOrCreate(
            ['email' => 'admin@whateverlove.id'],
            [
                'name' => 'Admin WHATEVERLOVE',
                'password' => 'password',
            ]
        );

        $products = [
            [
                'name' => 'T-Shirt Whatever Classic',
                'description' => 'T-shirt oversized dengan bahan cotton combed 24s yang adem dan nyaman dipakai sehari-hari.',
                'price' => 149000,
                'category' => 'baju',
                'badge' => 'new',
            ],
            [
                'name' => 'Oversized Tee Off White',
                'description' => 'Kaos oversized warna off white dengan printing depan yang premium.',
                'price' => 159000,
                'category' => 'baju',
            ],
            [
                'name' => 'Cargo Pants Black',
                'description' => 'Celana cargo dengan bahan twill premium, banyak kantong, cocok untuk gaya streetwear.',
                'price' => 249000,
                'category' => 'celana',
                'badge' => 'sold_out',
            ],
            [
                'name' => 'Sweatpants Bone White',
                'description' => 'Celana training dengan bahan fleece tebal dan tali serut.',
                'price' => 199000,
                'category' => 'celana',
            ],
            [
                'name' => 'Hoodie Heavyweight Navy',
                'description' => 'Hoodie dengan bahan fleece 500gsm yang tebal, hangat, dan awet formanya.',
                'price' => 349000,
                'category' => 'hoodie',
            ],
            [
                'name' => 'Hoodie Zip-Up Grey',
                'description' => 'Hoodie resleting dengan bahan fleece yang soft dan tidak mudah melar.',
                'price' => 329000,
                'category' => 'hoodie',
            ],
            [
                'name' => 'Cap Tulip Logo',
                'description' => 'Trucker cap dengan bordir logo brand di bagian depan.',
                'price' => 129000,
                'category' => 'aksesoris',
            ],
            [
                'name' => 'Socks 3 Pair Pack',
                'description' => 'Paket 3 pasang kaos kaki dengan logo brand, bahan katun tebal.',
                'price' => 89000,
                'category' => 'aksesoris',
            ],
            [
                'name' => 'Tee Halloween Skull',
                'description' => 'Kaos edisi Halloween dengan print skull glows-in-the-dark.',
                'price' => 179000,
                'category' => 'baju',
                'badge' => 'new',
                'article_id' => $articleIds['halloween'],
            ],
            [
                'name' => 'Hoodie Harbinger',
                'description' => 'Hoodie oversized edisi Halloween dengan print jack-o-lantern di punggung.',
                'price' => 379000,
                'category' => 'hoodie',
                'badge' => 'sold_out',
                'article_id' => $articleIds['halloween'],
            ],
            [
                'name' => 'Tee Merry & Bright',
                'description' => 'Kaos edisi Natal dengan print pohon dan snowflake, warna hangat.',
                'price' => 169000,
                'category' => 'baju',
                'article_id' => $articleIds['natal'],
            ],
            [
                'name' => 'Cap Festive Santa',
                'description' => 'Trucker cap edisi Natal dengan bordir Santa crest.',
                'price' => 139000,
                'category' => 'topi',
                'badge' => 'new',
                'article_id' => $articleIds['natal'],
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}