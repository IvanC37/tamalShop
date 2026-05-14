CREATE TABLE IF NOT EXISTS productos (

    id SERIAL PRIMARY KEY,

    nombre VARCHAR(255) NOT NULL,

    categoria VARCHAR(255) NOT NULL

);

CREATE TABLE IF NOT EXISTS gastos (

    id SERIAL PRIMARY KEY,

    usuario VARCHAR(255) NOT NULL,

    producto VARCHAR(255) NOT NULL,

    categoria VARCHAR(255) NOT NULL,

    monto NUMERIC(10,2) NOT NULL,

    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

INSERT INTO productos (nombre, categoria)
VALUES
('Orejas de pollo', 'Mascotas'),
('Tamal', 'Comida'),
('Jugo', 'Bebidas')
ON CONFLICT DO NOTHING;