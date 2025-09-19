// tipos para Laravel Echo e Pusher
declare global {
    interface Window {
        Echo: any;
        Pusher: any;
    }
}

// tipos para eventos de DM
export interface DirectMessageEvent {
    message: {
        id: number;
        direct_conversation_id: number;
        body: string;
        sender_id: number;
        created_at: string;
        updated_at: string;
        sender: {
            id: number;
            name: string;
        };
    };
}
