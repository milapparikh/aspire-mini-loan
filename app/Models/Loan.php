<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loan';

    protected $fillable = ['id_user','amount','term','application_date'];

}