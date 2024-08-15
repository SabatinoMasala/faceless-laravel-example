import {Composition} from 'remotion';
import {FacelessVideo} from './FacelessVideo';

export const RemotionRoot = () => {
	return (
		<>
			<Composition
				id="FacelessVideo"
				component={FacelessVideo}
				width={1080}
				height={1920}
                calculateMetadata={async ({ props }) => {
                    const data = await fetch(props.json);
                    const json = await data.json();
                    return {
                        fps: props.fps,
                        durationInFrames: Math.ceil(json.duration_in_seconds * props.fps),
                        props: {
                            ...props,
                            data: json,
                        },
                    };
                }}
			/>
		</>
	);
};
