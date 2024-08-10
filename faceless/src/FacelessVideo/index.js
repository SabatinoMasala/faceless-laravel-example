import {AbsoluteFill, interpolate, random, Audio, Img, Sequence, useCurrentFrame, useVideoConfig} from 'remotion';

export const FacelessVideo = ({data}) => {
    const frame = useCurrentFrame();
    const {durationInFrames, fps} = useVideoConfig();

    const images = data.images.map((image, index) => {
        const {start, end} = data.voice_over_chunks.groups[index];
        return {
            src: image.image_path,
            durationInFrames: (end - start) * fps,
            from: start * fps,
        };
    });

    const audio = `http://faceless-laravel-example.test/${data.voice_over_path}`;

    return (
        <AbsoluteFill style={{backgroundColor: 'white'}}>
            <Audio src={audio} />
            {frame}
            {images.map((image, index) => {
                const startRotation = (random(index) * 10) - 5;
                console.log(startRotation)
                const scale = interpolate(frame, [image.from, image.from + image.durationInFrames], [1.4, 1.2], {
                    extrapolateRight: 'clamp',
                });
                const rotation = interpolate(frame, [image.from, image.from + image.durationInFrames], [startRotation, 0], {
                    extrapolateRight: 'clamp',
                });
                return <Sequence durationInFrames={image.durationInFrames} from={image.from} style={{transform: `scale(${scale}) rotate(${rotation}deg)`}}>
                    <Img src={image.src} />
                </Sequence>
            })}
        </AbsoluteFill>
    );
};
