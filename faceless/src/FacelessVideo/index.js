import {AbsoluteFill, interpolate, random, Audio, Img, Sequence, useCurrentFrame, useVideoConfig} from 'remotion';

export const FacelessVideo = ({data}) => {
    const frame = useCurrentFrame();
    const {durationInFrames, fps} = useVideoConfig();

    const images = data.images.map((image, index) => {
        const {text, start, end} = data.voice_over_chunks.groups[index];
        return {
            src: image.image_path,
            text: text,
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
                    <AbsoluteFill>
                        <div style={{background: '#f00', fontSize: 80}}>{image.text}</div>
                    </AbsoluteFill>
                </Sequence>
            })}
        </AbsoluteFill>
    );
};
