export function required(): (value: string) => boolean|string {
    return (value) => Boolean(value) || 'Field is required'
}
export function maxLength(length: number): (value: string) => boolean|string {
    return (value) => value.length <= length || `Field cannot be longer than ${length} characters`
}
