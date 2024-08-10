import {AbsoluteFill, Audio, Img, Sequence, staticFile, useCurrentFrame, useVideoConfig} from 'remotion';

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
            {images.map((image, index) => <Sequence durationInFrames={image.durationInFrames} from={image.from}>
                <Img src={image.src} />
            </Sequence>)}
        </AbsoluteFill>
    );
};
