import {Composition} from 'remotion';
import {FacelessVideo} from './FacelessVideo';

const FPS = 1;

export const RemotionRoot = () => {
	return (
		<>
			<Composition
				id="FacelessVideo"
				component={FacelessVideo}
				fps={FPS}
				width={1080}
				height={1920}
                calculateMetadata={async ({ props }) => {
                    const data = await fetch(props.json);
                    const json = await data.json();
                    return {
                        durationInFrames: Math.ceil(json.duration_in_seconds * FPS),
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
