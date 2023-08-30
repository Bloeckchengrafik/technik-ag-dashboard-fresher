export interface FullEvent {
    id: number;
    organizer_id: number;
    type_id: number;
    room_id: number;
    title: string;
    description: string;
    needs_consultation: boolean;
    from_time: string;
    to_time: string;
    construction_from: string;
    construction_to: string;
    dismantling_from: string;
    dismantling_to: string;
    disabled: boolean;
    organizer: User;
    room: string;
    type: string;
    hdr_unsplash_id: string;
    shifts: Shift[];
    presets: Preset[];
    logs: Log[];
}

export interface Log {
    id: number;
    event_id: number;
    user_id: number;
    user_name: string;
    timestamp: string;
    message: string;
    type: "chat" | "change";
}

export interface User {
    id: number;
    firstname: string;
    lastname: string;
    email: string;
    permission: string[];
    groups: any[];
}

export interface Preset {
    id: number;
    tech: string;
}

export interface Shift {
    id: number;
    event_id: number;
    name: string;
    needed: number;
    from_time: string;
    to_time: string;
    users: User[];
}
