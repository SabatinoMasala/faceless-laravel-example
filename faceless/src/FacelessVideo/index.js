import {AbsoluteFill, interpolate, random, Audio, Img, Sequence, useCurrentFrame, useVideoConfig} from 'remotion';
import {Word} from "./Word";

export const FacelessVideo = ({data}) => {
    const frame = useCurrentFrame();
    const {durationInFrames, fps} = useVideoConfig();

    const images = data.images.map((image, index) => {
        const {text, start, end} = data.voice_over_chunks.groups[index];
        const isLast = data.images.length - 1 === index;
        let imageDuration = (end - start) * fps;
        if (isLast) {
            imageDuration = durationInFrames - start * fps;
        }
        return {
            src: image.image_path,
            text,
            durationInFrames: imageDuration,
            from: start * fps,
        };
    });

    const captionChunks = [];
    let count = 0;
    let currentChunk = [];
    data.voice_over_transcription.chunks.forEach(word => {
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

    const sentences = data.voice_over_chunks.sentences.map((sentence, index) => {
        const {text, start, end} = sentence;
        const isLast = data.voice_over_chunks.sentences.length - 1 === index;
        let sentenceDuration = (end - start) * fps;
        if (isLast) {
            sentenceDuration = durationInFrames - start * fps;
        }
        return {
            text,
            durationInFrames: sentenceDuration,
            from: start * fps,
        };
    });

    const audio = `http://faceless-laravel-example.test/${data.voice_over_path}`;

    return (
        <AbsoluteFill style={{backgroundColor: 'white'}}>
            <Audio src={audio} />
            {images.map((image, index) => {
                const startRotation = (random(index) * 4) - 2;
                const scale = interpolate(frame, [image.from, image.from + image.durationInFrames], [1.4, 1.2], {
                    extrapolateRight: 'clamp',
                });
                const rotation = interpolate(frame, [image.from, image.from + image.durationInFrames], [startRotation, 0], {
                    extrapolateRight: 'clamp',
                });
                return <Sequence durationInFrames={image.durationInFrames} from={image.from}>
                    <Img src={image.src} style={{transform: `scale(${scale}) rotate(${rotation}deg)`}} />
                </Sequence>
            })}
            {captionChunks.map((chunk) => {
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
            })}
        </AbsoluteFill>
    );
};
