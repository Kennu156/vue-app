export interface Marker {
    id: number;
    name: string;
    latitude: number;
    longitude: number;
    description: string | null;
    added: string;
    edited: string | null;
}