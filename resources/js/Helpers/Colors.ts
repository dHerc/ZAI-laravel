export function determineBorderColor(color: string|undefined, darkMode: boolean): string {
    const colorValues = getColorValues(color?.slice(1) || (darkMode ? '172439' : 'FFFFFF'))
    const change = darkMode ? 25 : -25;
    const newColors = colorValues.slice(0,3).map(
        (colorValue) => Math.min(255, Math.max(0, colorValue + change))
    )
    return '#' + [
        newColors[0],
        newColors[1],
        newColors[2],
        colorValues[3]
    ].map((colorValue) => colorValue.toString(16).padStart(2, '0')).join('')
}

export function determineTextColor(color: string|undefined, darkMode: boolean): string {
    const [r, g, b] = getColorValues(color?.slice(1) || (darkMode ? '172439' : 'FFFFFF'));
    const gamma = 2.2;
    const L = 0.2126 * Math.pow(r / 255, gamma)
        + 0.7152 * Math.pow(g / 255, gamma)
        + 0.0722 * Math.pow(b / 255, gamma);

    return L > 0.5 ? 'black' : 'white';
}

function getColorValues(color: string): number[] {
    if (color.length === 3) {
        return [
            parseInt(color[0], 16),
            parseInt(color[1], 16),
            parseInt(color[2], 16),
            255
        ]
    }
    return [
        parseInt(color.slice(0,2), 16),
        parseInt(color.slice(2,4), 16),
        parseInt(color.slice(4,6), 16),
        color.length > 6 ? parseInt(color.slice(6), 16) : 255
    ]
}
