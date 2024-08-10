<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreativeDirection extends MockableJob implements ShouldQueue
{

    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Story $story
    ){}

    protected function shouldMock(): bool
    {
        return true;
    }

    protected function execute()
    {
        $prompt = new \App\Prompts\CreativeDirection($this->story->content);
        $output = app('replicate')->run('meta/meta-llama-3.1-405b-instruct', [
            'prompt' => $prompt->get(),
            'max_tokens' => 1000,
        ]);
        return explode(PHP_EOL, collect($output)->join(''));
    }

    protected function mock()
    {
        return '**Characters**

* Emperor Julius Caesar: Muscular build, strong jawline, piercing brown eyes, distinctive nose, laurel wreath on head, ornate armor with gold accents, crimson cape flowing behind him, commanding presence.
* Roman Generals: Strong, rugged men with weathered skin, stern expressions, ornate armor with silver accents, flowing capes, loyal gaze towards Caesar.
* Roman Soldiers: Tired, battle-worn men with sweat-drenched faces, tattered armor, weary eyes, battered shields and swords.

**Locations**

* Battle-scarred landscape: Rolling hills, scorched earth, charred trees, scattered bodies, abandoned shields and armor, eerie mist.
* City of Zela: Ancient architecture, crumbling stone walls, terracotta rooftops, bustling streets, vibrant marketplaces, distant mountains.
* Roman Camp: Bustling tent city, roaring campfires, weary soldiers resting, makeshift fortifications, fluttering Roman banners.

**Concepts**

* Conquest: Broken armor, shattered shields, fallen enemies, triumphant Roman eagles, imposing Roman architecture.
* Power: Regal throne, imperial crown, scepter, ornate tapestries, imposing statues.
* Ambition: Blazing fire, burning torches, determined gaze, unyielding posture, unwavering resolve.

**Animals/Beings**

* Roman Eagles: Majestic birds with outstretched wings, sharp talons, piercing gaze, symbolizing Roman power and strength.
* War Horses: Muscular steeds with gleaming coats, flowing manes, determined eyes, carrying Roman soldiers into battle.

**Specific Elements**

* Laurels: Delicate, curved leaves, symbolizing victory and honor.
* Roman Banners: Crimson and gold, bearing the iconic Roman eagle, waving proudly in the wind.
* Caesar\'s Armor: Ornate, intricately designed, adorned with symbols of Roman power, reflecting his status as Emperor.';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->story->update([
            'creative_direction' => $this->handleOrMock(),
        ]);
    }
}
