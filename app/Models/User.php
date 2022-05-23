<?php

   namespace App\Models;

   use Illuminate\Contracts\Auth\MustVerifyEmail;
   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Foundation\Auth\User as Authenticatable;
   use Illuminate\Notifications\Notifiable;

//use Laravel\Sanctum\HasApiTokens;
   use Laravel\Passport\HasApiTokens;

   /**
    * @property mixed $id
    */
   class User extends Authenticatable
   {
      use HasApiTokens, HasFactory, Notifiable;

      /**
       * The attributes that are mass assignable.
       *
       * @var array<int, string>
       */
      protected $fillable = [
          'name',
          'email',
          'phone',
          'password',
      ];

      /**
       * The attributes that should be hidden for serialization.
       *
       * @var array<int, string>
       */
      protected $hidden = [
          'password',
          'remember_token',
          'updated_at',
          'created_at',
      ];

      /**
       * The attributes that should be cast.
       *
       * @var array<string, string>
       */
      /*protected $casts = [
          'email_verified_at' => 'datetime',
      ];*/

      /**
       * Validation rules
       *
       * @var array
       */
      public static $rulesRegister = [
          'name' => 'required',
          'email' => 'required|email|unique:users',
          'password' => 'required',
          'confirm_password' => 'required|same:password',
      ];

      /**
       * Validation rules
       *
       * @var array
       */
      public static $rulesLogin = [
          'email' => 'required|email|exists:users,email',
          'password' => 'required',
      ];
   }
