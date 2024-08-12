import { loadFont } from "@remotion/google-fonts/TitanOne";
const { fontFamily } = loadFont();

const wordStyle = {
    fontSize: 50,
    fontFamily, '-webkit-text-stroke': '2px black',
    fontWeight: 'bold',
    textShadow: '0 0 10px #000'
};

export const Word = ({word, isActive}) => {
    const style = {
        ...wordStyle,
        color: isActive ? '#F9332B' : 'white',
    };
    return <span style={style}>{word.text}</span>
}
