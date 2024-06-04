from flask import Flask, jsonify, request
import mysql.connector

app = Flask(__name__)

# Datenbank-Konfiguration
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'series'
}

def get_db_connection():
    try:
        connection = mysql.connector.connect(**db_config)
        return connection
    except mysql.connector.Error as e:
        print(f"Error connecting to the database: {e}")
        return None

# GET alle Serien
@app.route('/api/series', methods=['GET'])
def get_series():
    user_id = request.args.get('user_id')
    db = get_db_connection()
    if db is None:
        return jsonify({'error': 'Database connection error'}), 500
    
    try:
        cursor = db.cursor(dictionary=True)
        query = "SELECT * FROM series WHERE user_id = %s"
        cursor.execute(query, (user_id,))
        series = cursor.fetchall()
        cursor.close()
        db.close()
        return jsonify(series)
    except mysql.connector.Error as e:
        return jsonify({'error': f'Database error: {e}'}), 500

# GET einzelne Serie  
@app.route('/api/series/<int:series_id>', methods=['GET'])
def get_serie(series_id):
    user_id = request.args.get('user_id')
    db = get_db_connection()
    if db is None:
        return jsonify({'error': 'Database connection error'}), 500
    
    try:
        cursor = db.cursor(dictionary=True)
        query = "SELECT * FROM series WHERE series_id = %s AND user_id = %s"
        cursor.execute(query, (series_id, user_id))
        serie = cursor.fetchone()
        cursor.close()
        db.close()
        return jsonify(serie) if serie else jsonify({'message': 'Series not found'}), 404
    except mysql.connector.Error as e:
        return jsonify({'error': f'Database error: {e}'}), 500

# Seriensuch-Route
@app.route('/api/series/search', methods=['GET'])
def search_series():
    user_id = request.args.get('user_id')
    title = request.args.get('title')
    genre = request.args.get('genre')
    platform = request.args.get('platform')

    db = get_db_connection()
    if db is None:
        return jsonify({'error': 'Database connection error'}), 500
    
    try:
        cursor = db.cursor(dictionary=True)
        query = "SELECT * FROM series WHERE user_id = %s"
        params = [user_id]

        if title:
            query += " AND title LIKE %s"
            params.append(f"%{title}%")
        if genre:
            query += " AND genre LIKE %s"
            params.append(f"%{genre}%")
        if platform:
            query += " AND platform LIKE %s"
            params.append(f"%{platform}%")

        cursor.execute(query, tuple(params))
        series = cursor.fetchall()
        cursor.close()
        db.close()
        return jsonify(series)
    except mysql.connector.Error as e:
        return jsonify({'error': f'Database error: {e}'}), 500

# POST, PUT, DELETE Routen hier...

if __name__ == '__main__':
    app.run(debug=True)