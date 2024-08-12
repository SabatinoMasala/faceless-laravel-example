import {AbsoluteFill, Audio} from 'remotion';
import {Captions} from "./Captions";
import {Images} from "./Images";

export const FacelessVideo = ({data}) => {
    const audio = `http://faceless-laravel-example.test/${data.voice_over_path}`;
    return (
        <AbsoluteFill style={{backgroundColor: 'white'}}>
            <Audio src={audio} />
            <Images data={data} images={data.images} />
            <Captions wordChunks={data.voice_over_transcription.chunks} />
        </AbsoluteFill>
    );
};
