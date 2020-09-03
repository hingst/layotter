class Util {
    public clone<T>(object: T): T {
        return JSON.parse(JSON.stringify(object));
    }
}

export default new Util();
