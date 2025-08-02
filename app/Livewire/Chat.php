<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    public $users;
    public $selectedUser;
    public $newMessage;
    public $messages;


    public function mount()
    {
        $this->users = User::whereNot('id', Auth::id())->latest()->get();
        $this->selectedUser = $this->users->first();
        $this->loadMessages();
    }


    public function loadMessages()
    {
        $this->messages = ChatMessage::query()
            ->where(function ($q) {
                $q->where('sender_id', Auth::id())
                    ->where('receiver_id', $this->selectedUser->id);
            })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->selectedUser->id)
                    ->where('receiver_id', Auth::id());
            })->get();
    }

    public function selectUser(int $id)
    {
        $this->selectedUser = User::findOrFail($id);
        $this->loadMessages();
    }

    public function submit()
    {
        if (!$this->newMessage) {
            // session()->flash('error', "Can't Send Empty Messages");
            return;
        }
        // 'sender_id', 'receiver_id', 'message'
        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'message' => $this->newMessage
        ]);

        $this->messages->push($message);

        $this->newMessage = '';
    }


    public function render()
    {
        return view('livewire.chat');
    }
}
