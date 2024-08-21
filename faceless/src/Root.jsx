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
                    const fps = props.fps ? props.fps : 30;
                    let json;
                    if (!props.json) {
                        json = require('./example.json');
                    } else {
                        console.log(props.json);
                        const data = await fetch(props.json);
                        json = await data.json();
                    }
                    return {
                        fps,
                        durationInFrames: Math.ceil(json.duration_in_seconds * fps),
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
