<?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;
   use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    * @property mixed $belongsToAudio
    * @property mixed $audio
    */
   class Comment extends Model implements HasMedia
   {
      use HasFactory, InteractsWithMedia;

      protected $table = "comments";
      const COMMENT_PATH = "comment";

      /**
       * The attributes that are mass assignable.
       *
       * @var array
       */
      protected $fillable = [
          'user_id', 'audio_id', 'message', 'time',
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
         $media = $this->getMedia(Comment::COMMENT_PATH)->first();
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
       * @return BelongsTo
       */
      public function audio(): BelongsTo
      {
         return $this->belongsTo(Audio::class, 'audio_id')->with('author');
      }

      /**
       * Validation rules
       *
       * @var array
       */
      public static $rulesStore = [
          'time' => ['required', 'string', 'max:255'],
          'message' => ['required_without:source', 'string', 'max:255'],
          'source' => ['required_without:message', 'mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav,mp4', 'max:3072'],
      ];
   }
