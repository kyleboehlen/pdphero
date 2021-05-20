<?php

namespace App\View\Components\Habits;

use Illuminate\View\Component;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Habits\HistoryType;
use App\Helpers\Constants\Habits\Type;

class History extends Component
{
    public $create_update_form = true;
    public $habit;
    public $habit_history_array;
    public $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($habit, $offset = 0, $format = 'D')
    {
        $this->habit = $habit;
        $this->type = HistoryType::class;

        // Disable update form alerts on affirmations habits
        if($habit->type_id == Type::AFFIRMATIONS_HABIT || $habit->type_id == Type::JOURNALING_HABIT)
        {
            $this->create_update_form = false;
        }

        $this->habit_history_array = $this->habit->getHistoryArray($offset, $format);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.habits.history');
    }
}
