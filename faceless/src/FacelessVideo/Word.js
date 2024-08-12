import { loadFont } from "@remotion/google-fonts/TitanOne";
import {interpolateColors, useCurrentFrame} from "remotion";
const { fontFamily } = loadFont();

const wordStyle = {
    fontSize: 75,
    fontFamily, '-webkit-text-stroke': '2px black',
    fontWeight: 'bold',
    textShadow: '0 0 10px #000'
};

export const Word = ({word, frame}) => {
    const isActive = frame >= word.start && frame <= word.end;
    console.log(frame, word.text)
    const color = interpolateColors(frame, [word.start - 4, word.start], ['white', '#ffafae']);
    const style = {
        ...wordStyle,
        color,
    };
    return <span style={style}>{word.text}</span>
}
