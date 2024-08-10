import {Composition} from 'remotion';
import {FacelessVideo} from './FacelessVideo';

// Each <Composition> is an entry in the sidebar!

export const RemotionRoot = () => {
	return (
		<>
			<Composition
				// You can take the "id" to render a video:
				// npx remotion render src/index.jsx <id> out/video.mp4
				id="FacelessVideo"
				component={FacelessVideo}
				durationInFrames={99 * 30}
				fps={30}
				width={1080}
				height={1920}
                calculateMetadata={async ({ props }) => {
                    const data = await fetch(`http://faceless-laravel-example.test/api/story/8`);
                    const json = await data.json();
                    return {
                        props: {
                            ...props,
                            data: json,
                        },
                    };
                }}
				// You can override these props for each render:
				// https://www.remotion.dev/docs/parametrized-rendering
				defaultProps={{
					titleText: 'Welcome to Remotion',
					titleColor: 'black',
				}}
			/>
		</>
	);
};
