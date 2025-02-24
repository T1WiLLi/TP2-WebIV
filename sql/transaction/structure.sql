CREATE TYPE user_member AS ENUM ('NORMAL', 'PREMIUM');

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    firstname TEXT NOT NULL,
    lastname TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    balance DECIMAL(10, 2) DEFAULT 0,
    type user_member CHECK (type IN ('NORMAL', 'PREMIUM')) DEFAULT 'NORMAL'
);

CREATE TABLE token (
    id SERIAL PRIMARY KEY,
    value TEXT NOT NULL UNIQUE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE, -- Si l'utilisateur est supprimé, le token est supprimé
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE TABLE transactions (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE, -- Si l'utilisateur est supprimé, la transaction est supprimée
    name TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INTEGER NOT NULL,
    total DECIMAL (10, 2) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

CREATE INDEX idx_users_username ON users (username);
CREATE INDEX idx_token_user_id ON token (user_id);
CREATE INDEX idc_transaction_user_id ON transactions (user_id);