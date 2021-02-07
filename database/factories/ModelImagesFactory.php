<?php

namespace Database\Factories;

use App\Models\ModelImages;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModelImagesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ModelImages::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'original_name' => config('settings.product_default_image_file_name'),
            'image_path' => config('settings.product_default_image')
        ];
    }
}
