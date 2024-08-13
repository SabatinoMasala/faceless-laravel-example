import {Img, interpolate, random, Sequence, useCurrentFrame, useVideoConfig} from "remotion";

export const Images = ({images, data}) => {
    const frame = useCurrentFrame();
    const {durationInFrames, fps} = useVideoConfig();
    const panels = images.map((image, index) => {
        const {start, end} = image;
        const isLast = images.length - 1 === index;
        let imageDuration = (end - start) * fps;
        if (isLast) {
            imageDuration = durationInFrames - start * fps;
        }
        return {
            src: image.image_path,
            durationInFrames: imageDuration,
            from: start * fps,
        };
    });
    return panels.map((image, index) => {
        const startRotation = (random(index) * 4) - 2;
        const scale = interpolate(frame, [
            image.from,
            image.from + image.durationInFrames
        ], [1.4, 1.2], {
            extrapolateRight: 'clamp',
        });
        const rotation = interpolate(frame, [
            image.from,
            image.from + image.durationInFrames
        ], [startRotation, 0], {
            extrapolateRight: 'clamp',
        });
        return <Sequence durationInFrames={image.durationInFrames} from={image.from}>
            <Img src={image.src} style={{transform: `scale(${scale}) rotate(${rotation}deg)`}} />
        </Sequence>
    });
}
