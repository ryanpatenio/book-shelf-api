
Animal Model
class Animal extends Model
{
    // One animal can have one vaccine card
    public function vaccineCard()
    {
        return $this->hasOne(VaccineCard::class, 'animal_id');
    }

    // One animal can have many schedules (many-to-many)
    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'animal_schedule', 'animal_id', 'schedule_id');
    }

    // One animal can have many vaccine details (one-to-many)
    public function vaccineDetails()
    {
        return $this->hasMany(VaccineDetail::class, 'animal_id');
    }
}

// schedule Model
class Schedule extends Model
{
    // One schedule can belong to many animals (many-to-many)
    public function animals()
    {
        return $this->belongsToMany(Animal::class, 'animal_schedule', 'schedule_id', 'animal_id');
    }

    // Schedule belongs to a client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}

//Vaccine Card Mode
class VaccineCard extends Model
{
    // One vaccine card can have many vaccine details (one-to-many)
    public function vaccineDetails()
    {
        return $this->hasMany(VaccineDetail::class, 'vaccine_card_id');
    }

    // One vaccine card belongs to one animal (inverse of hasOne)
    public function animal()
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }
}

//vaccine Details Model
class VaccineDetail extends Model
{
    // Each vaccine detail belongs to one vaccine card
    public function vaccineCard()
    {
        return $this->belongsTo(VaccineCard::class, 'vaccine_card_id');
    }

    // Each vaccine detail belongs to one animal
    public function animal()
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }
}

public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')            // Foreign key for the client
                  ->constrained('clients')          // Assumes a 'clients' table exists
                  ->onDelete('cascade');            // Ensures schedules are deleted when a client is deleted
            $table->dateTime('scheduled_at');         // The date and time of the vaccination schedule
            $table->enum('status', ['pending', 'approved', 'completed'])->default('pending'); // Status of the schedule
            $table->timestamps();                    // Timestamps for created_at and updated_at
        });
    }


//this is the pivot table for schedules many to many relationship// animal_schedule table
Schema::create('animal_schedule', function (Blueprint $table) {
    $table->id();
    $table->foreignId('animal_id')->constrained('animals');
    $table->foreignId('schedule_id')->constrained('schedules');
    $table->timestamps();
});

$animal = Animal::find(1); // Find an animal by its ID
$schedules = $animal->schedules; // Get all schedules for the animal

$animal = Animal::find(1);
$vaccineCard = $animal->vaccineCard; // Get the vaccine card of the animal

$animal = Animal::find(1);
$vaccineDetails = $animal->vaccineDetails; // Get all vaccine details (history) for the animal


$schedule = Schedule::find(1); // Find a schedule by its ID
$animals = $schedule->animals; // Get all animals scheduled for this vaccination


Final Thoughts:
Use hasMany when you want to define a one-to-many relationship (e.g., an animal can have multiple vaccine records).
Use belongsToMany for a many-to-many relationship (e.g., an animal can have many vaccination schedules).
Pivot tables are essential for many-to-many relationships, such as between Animals and Schedules.