<?php

namespace Ingenious\Isupport;

class TicketFake {

    public $assignee;
    public $category;
    public $created_date;
    public $custom_fields;
    public $customer;
    public $department;
    public $problem;
    public $full_description;
    public $group;
    public $id;
    public $modified_date;
    public $number;
    public $priority;
    public $status;
    public $closed_date;
    public $total_time_worked;

    /**
     * Make a new fake ticket
     * @method __construct
     *
     * @return   void
     */
    public function __construct($faker)
    {
        $this->assignee = $faker->word;
        $this->category = $faker->word;
        $this->created_date = $faker->date;
        $this->modified_date = $faker->date;
        $this->closed_date = $faker->date;
        $this->custom_fields = [
            'computer_name' => $faker->word,
            'does_this_prevent_you_from_working_entirely' => $faker->word,
            'impact_to_the_borough' => $faker->sentence,
            'ticket_subject' => $faker->word,
            'urgency' => $faker->word
        ];
        $this->customer = $faker->word;
        $this->department = $faker->word;
        $this->problem = $faker->sentence;
        $this->full_description = $faker->paragraph;
        $this->group = $faker->word;
        $this->id = $faker->randomNumber;
        $this->total_time_worked = $faker->randomNumber;
        $this->number = $faker->word;
        $this->priority = $faker->word;
        $this->status = $faker->word;
    }



}
