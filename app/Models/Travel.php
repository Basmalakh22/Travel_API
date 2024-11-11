<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use League\CommonMark\Extension\Attributes\Node\Attributes;
use Spatie\Sluggable\HasSlug;

class Travel extends Model
{
    use HasFactory;
    protected $table = 'travels';
    protected $fillable = [
        'name',
        'description',
        'slug',
        'is_public',
        'number_of_days',
    ];
    public function tours()
    {
       return $this->hasMany(Tour::class);
    }
    public function numberOfNights():Attribute
    {
        return Attribute::make(
            get:fn($value ,$attributes) => $attributes['number_of_days']-1
        );
    }
}
