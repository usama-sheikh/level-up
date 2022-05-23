<?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;
   use Illuminate\Database\Eloquent\Relations\BelongsTo;
   use Illuminate\Database\Eloquent\Relations\HasMany;
   use Spatie\MediaLibrary\HasMedia;
   use Spatie\MediaLibrary\InteractsWithMedia;
   use Spatie\MediaLibrary\MediaCollections\Models\Media;

   /**
    * @method static create(array $input)
    * @method static find(int $id)
    * @method static where(string $string, $id)
    * @method static orderBy(string $string, string $string1)
    * @property mixed $name
    * @property mixed $detail
    * @property mixed $user_id
    * @property mixed $user
    * @property mixed $comments
    * @property mixed $hasManyComment
    */
   class Audio extends Model implements HasMedia
   {
      use HasFactory, InteractsWithMedia;

      protected $table = "audios";
      const AUDIO_PATH = "audio";

      /**
       * The attributes that are mass assignable.
       *
       * @var array
       */
      protected $fillable = [
          'user_id', 'surah', 'ayat',
      ];

      /**
       * The attributes that should be hidden for serialization.
       *
       * @var array<int, string>
       */
      protected $hidden = [
          'updated_at',
          'created_at',
         'media',
      ];

      protected $appends = ['source'];

      /**
       * @return mixed
       */
      public function getSourceAttribute()
      {
         $media = $this->getMedia(Audio::AUDIO_PATH)->first();
         if (!empty($media)) {
            return $media->getFullUrl();
         }

         return asset('assets/default.mp3');
      }

      /**
       * @return BelongsTo
       */
      public function author(): BelongsTo
      {
         return $this->belongsTo(User::class, 'user_id');
      }

      /**
       * @return HasMany
       */
      public function comments(): HasMany
      {
         return $this->hasMany(Comment::class, 'audio_id');
      }

      /**
       * Validation rules
       *
       * @var array
       */
      public static $rulesStore = [
          'surah' => ['required', 'string', 'max:255'],
          'ayat' => ['required', 'string', 'max:255'],
          'source' => ['required', 'mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav,mp4', 'max:3072'],
      ];
   }
