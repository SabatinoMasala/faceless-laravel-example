import {AbsoluteFill, Sequence, useCurrentFrame, useVideoConfig} from "remotion";
import {Word} from "./Word";

export const Captions = ({wordChunks}) => {
    const frame = useCurrentFrame();
    const {durationInFrames, fps} = useVideoConfig();
    const captionChunks = [];
    let count = 0;
    let currentChunk = [];
    wordChunks.forEach(word => {
        currentChunk.push({
            text: word.text,
            start: word.timestamp[0] * fps,
            end: word.timestamp[1] * fps,
        });
        count++;
        const wordContainsPunctuation = word.text.match(/[\.\?\!\,]/);
        if (count === 10 || count >= 3 && wordContainsPunctuation) {
            count = 0;
            captionChunks.push({
                start: currentChunk[0].start,
                end: currentChunk[currentChunk.length - 1].end,
                durationInFrames: currentChunk[currentChunk.length - 1].end - currentChunk[0].start,
                words: currentChunk
            });
            currentChunk = [];
        }
    });

    return captionChunks.map((chunk) => {
        return <Sequence durationInFrames={chunk.durationInFrames} from={chunk.start}>
            <AbsoluteFill>
                <div style={{textAlign: 'center', position: 'absolute', bottom: '20%', left: '10%', right: '10%'}}>
                    {chunk.words.map(word => {
                        const isActive = frame >= word.start && frame <= word.end;
                        return <Word word={word} isActive={isActive} />
                    })}
                </div>
            </AbsoluteFill>
        </Sequence>
    });
}
