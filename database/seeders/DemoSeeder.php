<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Fixed demo accounts (easy to log in with)
        $alice = User::factory()->create([
            'name'  => 'Alice Johnson',
            'email' => 'alice@demo.com',
            'password' => Hash::make('password'),
            'last_active_at' => now()->subMinutes(5),
        ]);

        $bob = User::factory()->create([
            'name'  => 'Bob Smith',
            'email' => 'bob@demo.com',
            'password' => Hash::make('password'),
            'last_active_at' => now()->subHours(1),
        ]);

        $carol = User::factory()->create([
            'name'  => 'Carol White',
            'email' => 'carol@demo.com',
            'password' => Hash::make('password'),
            'last_active_at' => now()->subDays(1),
        ]);

        // Extra random users
        $extras = User::factory(4)->create([
            'password' => Hash::make('password'),
        ]);

        $allUsers = collect([$alice, $bob, $carol])->merge($extras);

        // --- Direct conversations ---

        // Alice ↔ Bob
        $this->directConversation($alice, $bob, [
            [$alice, 'Hey Bob! How are you doing?'],
            [$bob,   'Hey Alice! I\'m great, thanks. You?'],
            [$alice, 'Doing well! Did you check the latest update?'],
            [$bob,   'Not yet, I\'ll look into it later today.'],
            [$alice, 'Sounds good, let me know what you think!'],
        ]);

        // Alice ↔ Carol
        $this->directConversation($alice, $carol, [
            [$carol, 'Alice, are you joining the meeting tomorrow?'],
            [$alice, 'Yes! What time does it start?'],
            [$carol, 'At 10am. Don\'t be late 😄'],
            [$alice, 'I\'ll be there on time, promise!'],
        ]);

        // Bob ↔ Carol
        $this->directConversation($bob, $carol, [
            [$bob,   'Carol, did you finish the report?'],
            [$carol, 'Almost done, sending it by end of day.'],
            [$bob,   'Perfect, thanks!'],
        ]);

        // Alice with random users
        foreach ($extras->take(2) as $user) {
            $this->directConversation($alice, $user, [
                [$alice, 'Hi there! Welcome to the team.'],
                [$user,  'Thanks, happy to be here!'],
                [$alice, 'Let me know if you need anything.'],
            ]);
        }

        // --- Group conversations ---

        // Group: Alice, Bob, Carol
        $teamChat = Conversation::factory()->group()->create(['name' => 'Team Chat']);
        $teamChat->users()->attach([$alice->id, $bob->id, $carol->id]);
        $this->seedMessages($teamChat, [
            [$alice, 'Welcome to the team chat everyone! 👋'],
            [$bob,   'Great to have this!'],
            [$carol, 'Hey all! Excited to work together.'],
            [$alice, 'Let\'s keep this channel for important updates.'],
            [$bob,   'Agreed. Will share the roadmap shortly.'],
            [$carol, 'Looking forward to it!'],
        ]);

        // Group: all users
        $general = Conversation::factory()->group()->create(['name' => 'General']);
        $general->users()->attach($allUsers->pluck('id')->toArray());
        $this->seedMessages($general, [
            [$alice, 'Hey everyone, this is the general channel!'],
            [$bob,   'Nice, good to have everyone here.'],
            [$carol, 'Hi all! 👋'],
            [$extras[0], 'Hello! Great to meet you all.'],
            [$extras[1], 'Excited to be here!'],
            [$alice, 'Feel free to chat anytime.'],
            [$bob,   'Will do!'],
        ]);
    }

    private function directConversation(User $a, User $b, array $messages): Conversation
    {
        $conversation = Conversation::factory()->create();
        $conversation->users()->attach([$a->id, $b->id]);
        $this->seedMessages($conversation, $messages);

        return $conversation;
    }

    private function seedMessages(Conversation $conversation, array $messages): void
    {
        $createdAt = now()->subMinutes(count($messages) * 3);

        foreach ($messages as [$sender, $text]) {
            ChatMessage::factory()->create([
                'user_id'         => $sender->id,
                'conversation_id' => $conversation->id,
                'message'         => $text,
                'created_at'      => $createdAt,
                'updated_at'      => $createdAt,
            ]);

            $createdAt = $createdAt->addMinutes(rand(1, 5));
        }

        $conversation->touch();
    }
}
