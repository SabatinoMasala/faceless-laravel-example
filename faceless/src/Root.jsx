import {Composition} from 'remotion';
import {FacelessVideo} from './FacelessVideo';

const FPS = 30;

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
                    const data = await fetch(`http://faceless-laravel-example.test/api/story/2`);
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
