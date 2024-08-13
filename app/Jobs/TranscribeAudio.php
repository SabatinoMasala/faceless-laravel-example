<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use SabatinoMasala\Replicate\Replicate;

class TranscribeAudio extends MockableJob implements ShouldQueue
{
    use Queueable;

    protected $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Story $story
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->story->update([
            'status' => 'TRANSCRIBE_START',
        ]);
        $this->story->update([
            'voice_over_transcription' => $this->handleOrMock(),
            'status' => 'TRANSCRIBE_END',
        ]);
    }

    protected function mock()
    {
        return json_decode('{"chunks":[{"text":" As","timestamp":[0,0.08]},{"text":" the","timestamp":[0.08,0.22]},{"text":" sun","timestamp":[0.22,0.46]},{"text":" dipped","timestamp":[0.46,0.7]},{"text":" into","timestamp":[0.7,0.98]},{"text":" the","timestamp":[0.98,1.12]},{"text":" horizon,","timestamp":[1.12,1.72]},{"text":" casting","timestamp":[1.72,2.24]},{"text":" a","timestamp":[2.24,2.42]},{"text":" golden","timestamp":[2.42,2.78]},{"text":" glow","timestamp":[2.78,3.08]},{"text":" over","timestamp":[3.08,3.5]},{"text":" the","timestamp":[3.5,3.62]},{"text":" battle","timestamp":[3.62,3.9]},{"text":"-scarred","timestamp":[3.9,4.26]},{"text":" landscape,","timestamp":[4.26,5.18]},{"text":" Emperor","timestamp":[5.18,5.66]},{"text":" Julius","timestamp":[5.66,5.96]},{"text":" Caesar","timestamp":[5.96,6.48]},{"text":" stood","timestamp":[6.48,6.86]},{"text":" victorious","timestamp":[6.86,7.46]},{"text":" atop","timestamp":[7.46,8.02]},{"text":" a","timestamp":[8.02,8.14]},{"text":" hill,","timestamp":[8.14,8.56]},{"text":" gazing","timestamp":[8.56,9.22]},{"text":" out","timestamp":[9.22,9.44]},{"text":" at","timestamp":[9.44,9.64]},{"text":" the","timestamp":[9.64,9.74]},{"text":" sprawling","timestamp":[9.74,10.18]},{"text":" city","timestamp":[10.18,10.46]},{"text":" of","timestamp":[10.46,10.72]},{"text":" Zella.","timestamp":[10.72,11.2]},{"text":" The","timestamp":[11.2,11.9]},{"text":" air","timestamp":[11.9,12.16]},{"text":" was","timestamp":[12.16,12.36]},{"text":" heavy","timestamp":[12.36,12.72]},{"text":" with","timestamp":[12.72,12.94]},{"text":" the","timestamp":[12.94,13.04]},{"text":" scent","timestamp":[13.04,13.28]},{"text":" of","timestamp":[13.28,13.4]},{"text":" smoke","timestamp":[13.4,13.72]},{"text":" and","timestamp":[13.72,13.96]},{"text":" sweat,","timestamp":[13.96,14.46]},{"text":" the","timestamp":[14.46,14.66]},{"text":" sound","timestamp":[14.66,14.98]},{"text":" of","timestamp":[14.98,15.14]},{"text":" clashing","timestamp":[15.14,15.52]},{"text":" steel,","timestamp":[15.52,16.04]},{"text":" and","timestamp":[16.04,16.3]},{"text":" the","timestamp":[16.3,16.36]},{"text":" cries","timestamp":[16.36,16.66]},{"text":" of","timestamp":[16.66,16.9]},{"text":" the","timestamp":[16.9,17]},{"text":" fallen","timestamp":[17,17.32]},{"text":" still","timestamp":[17.32,17.62]},{"text":" echoing","timestamp":[17.62,18.22]},{"text":" in","timestamp":[18.22,18.3]},{"text":" his","timestamp":[18.3,18.42]},{"text":" mind.","timestamp":[18.42,18.96]},{"text":" With","timestamp":[18.96,19.26]},{"text":" a","timestamp":[19.26,19.38]},{"text":" deep","timestamp":[19.38,19.54]},{"text":" breath,","timestamp":[19.54,20]},{"text":" he","timestamp":[20,20.1]},{"text":" raised","timestamp":[20.1,20.34]},{"text":" his","timestamp":[20.34,20.52]},{"text":" arms","timestamp":[20.52,20.78]},{"text":" to","timestamp":[20.78,20.98]},{"text":" the","timestamp":[20.98,21.06]},{"text":" sky","timestamp":[21.06,21.32]},{"text":" and","timestamp":[21.32,21.56]},{"text":" bellowed","timestamp":[21.56,21.9]},{"text":" the","timestamp":[21.9,22]},{"text":" words","timestamp":[22,22.3]},{"text":" that","timestamp":[22.3,22.48]},{"text":" would","timestamp":[22.48,22.6]},{"text":" become","timestamp":[22.6,22.84]},{"text":" his","timestamp":[22.84,23.08]},{"text":" legend,","timestamp":[23.08,23.82]},{"text":" Veni,","timestamp":[23.82,24.58]},{"text":" Vidi,","timestamp":[24.58,25.32]},{"text":" Vici.","timestamp":[25.32,25.98]},{"text":" I","timestamp":[25.98,26.5]},{"text":" came,","timestamp":[26.5,27.1]},{"text":" I","timestamp":[27.1,27.36]},{"text":" saw,","timestamp":[27.36,27.94]},{"text":" I","timestamp":[27.94,28.28]},{"text":" conquered.","timestamp":[28.28,28.88]},{"text":" The","timestamp":[28.88,29.24]},{"text":" phrase","timestamp":[29.24,29.52]},{"text":" thundered","timestamp":[29.52,30.14]},{"text":" through","timestamp":[30.14,30.36]},{"text":" the","timestamp":[30.36,30.48]},{"text":" valleys,","timestamp":[30.48,31]},{"text":" striking","timestamp":[31,31.48]},{"text":" fear","timestamp":[31.48,31.88]},{"text":" into","timestamp":[31.88,32.28]},{"text":" the","timestamp":[32.28,32.4]},{"text":" hearts","timestamp":[32.4,32.68]},{"text":" of","timestamp":[32.68,32.8]},{"text":" his","timestamp":[32.8,32.92]},{"text":" enemies","timestamp":[32.92,33.26]},{"text":" and","timestamp":[33.26,33.74]},{"text":" inspiring","timestamp":[33.74,34.14]},{"text":" his","timestamp":[34.14,34.56]},{"text":" troops","timestamp":[34.56,34.88]},{"text":" to","timestamp":[34.88,35.2]},{"text":" greater","timestamp":[35.2,35.42]},{"text":" heights.","timestamp":[35.42,36.44]},{"text":" Caesar\'s","timestamp":[36.44,37.1]},{"text":" campaign","timestamp":[37.1,37.44]},{"text":" had","timestamp":[37.44,37.86]},{"text":" been","timestamp":[37.86,38.04]},{"text":" long","timestamp":[38.04,38.36]},{"text":" and","timestamp":[38.36,38.64]},{"text":" brutal,","timestamp":[38.64,39.14]},{"text":" but","timestamp":[39.14,39.62]},{"text":" with","timestamp":[39.62,39.76]},{"text":" this","timestamp":[39.76,40]},{"text":" triumph,","timestamp":[40,40.6]},{"text":" he","timestamp":[40.6,40.82]},{"text":" had","timestamp":[40.82,40.96]},{"text":" secured","timestamp":[40.96,41.24]},{"text":" a","timestamp":[41.24,41.46]},{"text":" vital","timestamp":[41.46,41.74]},{"text":" trade","timestamp":[41.74,42.06]},{"text":" route","timestamp":[42.06,42.34]},{"text":" and","timestamp":[42.34,42.84]},{"text":" cemented","timestamp":[42.84,43.32]},{"text":" his","timestamp":[43.32,43.44]},{"text":" position","timestamp":[43.44,43.86]},{"text":" as","timestamp":[43.86,44.38]},{"text":" the","timestamp":[44.38,44.5]},{"text":" greatest","timestamp":[44.5,44.8]},{"text":" leader","timestamp":[44.78,45.08]},{"text":" Rome","timestamp":[45.08,45.42]},{"text":" had","timestamp":[45.42,45.62]},{"text":" ever","timestamp":[45.62,45.88]},{"text":" known.","timestamp":[45.88,46.5]},{"text":" As","timestamp":[46.5,47.76]},{"text":" the","timestamp":[47.76,47.9]},{"text":" stars","timestamp":[47.9,48.2]},{"text":" began","timestamp":[48.2,48.48]},{"text":" to","timestamp":[48.48,48.7]},{"text":" twinkle","timestamp":[48.7,49]},{"text":" in","timestamp":[49,49.18]},{"text":" the","timestamp":[49.18,49.24]},{"text":" night","timestamp":[49.24,49.42]},{"text":" sky,","timestamp":[49.42,49.96]},{"text":" he","timestamp":[49.96,50.34]},{"text":" knew","timestamp":[50.34,50.58]},{"text":" that","timestamp":[50.58,50.74]},{"text":" his","timestamp":[50.74,50.88]},{"text":" name","timestamp":[50.88,51.16]},{"text":" would","timestamp":[51.16,51.32]},{"text":" be","timestamp":[51.32,51.5]},{"text":" etched","timestamp":[51.5,51.86]},{"text":" in","timestamp":[51.86,52.04]},{"text":" history","timestamp":[52.04,52.4]},{"text":" forever,","timestamp":[52.4,53.08]},{"text":" synonymous","timestamp":[53.08,53.82]},{"text":" with","timestamp":[53.82,54.16]},{"text":" power,","timestamp":[54.16,54.76]},{"text":" strategy,","timestamp":[54.76,55.76]},{"text":" and","timestamp":[55.76,56.24]},{"text":" unyielding","timestamp":[56.24,56.86]},{"text":" ambition.","timestamp":[56.86,57.62]},{"text":" With","timestamp":[57.62,58.38]},{"text":" a","timestamp":[58.38,58.5]},{"text":" satisfied","timestamp":[58.5,58.82]},{"text":" smile,","timestamp":[58.82,59.5]},{"text":" Caesar","timestamp":[59.5,59.7]},{"text":" turned","timestamp":[59.7,60.08]},{"text":" to","timestamp":[60.08,60.26]},{"text":" his","timestamp":[60.26,60.38]},{"text":" loyal","timestamp":[60.38,60.58]},{"text":" generals,","timestamp":[60.58,61.24]},{"text":" his","timestamp":[61.24,61.48]},{"text":" eyes","timestamp":[61.48,61.82]},{"text":" aglow","timestamp":[61.82,62.22]},{"text":" with","timestamp":[62.22,62.48]},{"text":" the","timestamp":[62.48,62.58]},{"text":" fire","timestamp":[62.58,62.82]},{"text":" of","timestamp":[62.82,63.02]},{"text":" conquest.","timestamp":[63.02,63.8]},{"text":" We","timestamp":[63.8,64.2]},{"text":" have","timestamp":[64.2,64.34]},{"text":" won","timestamp":[64.34,64.56]},{"text":" the","timestamp":[64.56,64.7]},{"text":" day,","timestamp":[64.7,64.98]},{"text":" he","timestamp":[64.98,65.1]},{"text":" said,","timestamp":[65.1,65.5]},{"text":" his","timestamp":[65.5,65.72]},{"text":" voice","timestamp":[65.72,66]},{"text":" unyielding,","timestamp":[73.28,73.9]},{"text":" with","timestamp":[73.9,74.3]},{"text":" Caesar","timestamp":[74.3,74.56]},{"text":" at","timestamp":[74.56,74.78]},{"text":" its","timestamp":[74.78,74.92]},{"text":" helm,","timestamp":[74.92,75.3]},{"text":" forever","timestamp":[75.3,75.6]},{"text":" changed","timestamp":[75.6,76.04]},{"text":" by","timestamp":[76.04,76.32]},{"text":" the","timestamp":[76.32,76.44]},{"text":" conqueror\'s","timestamp":[76.44,76.96]},{"text":" cry,","timestamp":[76.96,77.48]},{"text":" Vini,","timestamp":[77.48,78.28]},{"text":" Vidi,","timestamp":[78.28,78.86]},{"text":" Vici.","timestamp":[78.86,79.5]}],"text":" As the sun dipped into the horizon, casting a golden glow over the battle-scarred landscape, Emperor Julius Caesar stood victorious atop a hill, gazing out at the sprawling city of Zella. The air was heavy with the scent of smoke and sweat, the sound of clashing steel, and the cries of the fallen still echoing in his mind. With a deep breath, he raised his arms to the sky and bellowed the words that would become his legend, Veni, Vidi, Vici. I came, I saw, I conquered. The phrase thundered through the valleys, striking fear into the hearts of his enemies and inspiring his troops to greater heights. Caesar\'s campaign had been long and brutal, but with this triumph, he had secured a vital trade route and cemented his position as the greatest leader Rome had ever known. As the stars began to twinkle in the night sky, he knew that his name would be etched in history forever, synonymous with power, strategy, and unyielding ambition. With a satisfied smile, Caesar turned to his loyal generals, his eyes aglow with the fire of conquest. We have won the day, he said, his voice unyielding, with Caesar at its helm, forever changed by the conqueror\'s cry, Vini, Vidi, Vici."}', true);
    }

    protected function shouldMock(): bool
    {
        return env('SHOULD_MOCK_STORY', false);
    }

    public function execute(Replicate $replicate)
    {
        return $replicate->run('vaibhavs10/incredibly-fast-whisper:3ab86df6c8f54c11309d4d1f930ac292bad43ace52d10c80d87eb258b3c9f79c', [
            'audio' => env('NGROK_URL') . Storage::url($this->story->voice_over_path),
            'timestamp' => 'word',
        ]);
    }

    public function failed()
    {
        $this->story->update([
            'status' => 'TRANSCRIBE_FAILED',
        ]);
    }

}
