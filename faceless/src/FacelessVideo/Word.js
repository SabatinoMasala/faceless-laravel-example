import { loadFont } from "@remotion/google-fonts/TitanOne";
import {interpolate, interpolateColors, useCurrentFrame} from "remotion";
const { fontFamily } = loadFont();

const wordStyle = {
    fontSize: 75,
    fontFamily, '-webkit-text-stroke': '2px black',
    fontWeight: 'bold',
    textShadow: '0 0 10px #000',
    zIndex: 1,
    position: 'relative',
};

const wrapperStyle = {
    display: 'inline-block',
    paddingLeft: 10,
    paddingRight: 10,
    position: 'relative',
};

export const Word = ({word, frame}) => {
    const color = interpolateColors(frame, [word.start - 4, word.start], ['white', '#ffafae']);
    const style = {
        ...wordStyle,
        color,
    };
    return <div style={wrapperStyle}>
        <span style={style}>{word.text}</span>
    </div>
}
