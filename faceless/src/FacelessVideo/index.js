import {AbsoluteFill, interpolate, random, Audio, Img, Sequence, useCurrentFrame, useVideoConfig} from 'remotion';
import { loadFont } from "@remotion/google-fonts/TitanOne";
const { fontFamily } = loadFont();

export const FacelessVideo = ({data}) => {
    const frame = useCurrentFrame();
    const {durationInFrames, fps} = useVideoConfig();

    const images = data.images.map((image, index) => {
        const {text, start, end} = data.voice_over_chunks.groups[index];
        return {
            src: image.image_path,
            text,
            durationInFrames: (end - start) * fps,
            from: start * fps,
        };
    });

    const sentences = data.voice_over_chunks.sentences.map((sentence, index) => {
        const {text, start, end} = sentence;
        return {
            text,
            durationInFrames: (end - start) * fps,
            from: start * fps,
        };
    });

    const audio = `http://faceless-laravel-example.test/${data.voice_over_path}`;

    return (
        <AbsoluteFill style={{backgroundColor: 'white'}}>
            <Audio src={audio} />
            {images.map((image, index) => {
                const startRotation = (random(index) * 10) - 5;
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
            {sentences.map((sentence) => {
                return <Sequence durationInFrames={sentence.durationInFrames} from={sentence.from}>
                    <AbsoluteFill style={{alignItems: 'center', justifyContent: 'center'}}>
                        <div style={{padding: 20, fontFamily, background: 'rgba(0, 0, 0, 0.25)', color: '#fff', fontSize: 60, textAlign: 'center'}}>{sentence.text}</div>
                    </AbsoluteFill>
                </Sequence>
            })}
        </AbsoluteFill>
    );
};
