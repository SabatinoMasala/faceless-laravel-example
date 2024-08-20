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

const bgStyle = {
    background: 'rgba(0, 0, 0, 0.75)',
    position: 'absolute',
    top: -5,
    left: -5,
    right: -5,
    bottom: -5,
    zIndex: 0,
    borderRadius: 5,
};

export const Word = ({word, frame}) => {
    const color = interpolateColors(frame, [word.start - 4, word.start], ['white', '#ffafae']);
    const opacity = interpolate(frame, [word.start - 4, word.start, word.end], [0, 1, 0]);
    const style = {
        ...wordStyle,
        color,
    };
    return <div style={wrapperStyle}>
        <span style={style}>{word.text}</span>
        <div style={{...bgStyle, opacity}}></div>
    </div>
}
