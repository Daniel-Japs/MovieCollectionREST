from flask import Flask, jsonify, request
import mysql.connector

app = Flask(__name__)

# Datenbank-Konfiguration
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'series2'
}

def get_db_connection():
    try:
        connection = mysql.connector.connect(**db_config)
        return connection
    except mysql.connector.Error as e:
        print(f"Error connecting to the database: {e}")
        return None

@app.route('/api/login', methods=['POST'])
def login():
    data = request.get_json()
    username = data['username']
    password = data['password']

    db = get_db_connection()
    if db is None:
        return jsonify({'success': False, 'error': 'Database connection error'}), 500
    
    try:
        cursor = db.cursor(dictionary=True)
        query = "SELECT * FROM users WHERE username = %s AND password = %s"
        cursor.execute(query, (username, password))
        user = cursor.fetchone()
        cursor.close()
        db.close()

        if user:
            return jsonify({'success': True, 'user_id': user['user_id']})
        else:
            return jsonify({'success': False, 'error': 'Invalid username or password'})
    except mysql.connector.Error as e:
        return jsonify({'success': False, 'error': f'Database error: {e}'}), 500

@app.route('/api/series', methods=['GET'])
def get_series():
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

@app.route('/api/series', methods=['POST'])
def add_series():
    data = request.get_json()
    user_id = data['user_id']
    title = data['title']
    seasons = data['seasons']
    genre = data['genre']
    platform = data['platform']

    db = get_db_connection()
    if db is None:
        return jsonify({'error': 'Database connection error'}), 500
    
    try:
        cursor = db.cursor()
        query = "INSERT INTO series (user_id, title, seasons, genre, platform) VALUES (%s, %s, %s, %s, %s)"
        values = (user_id, title, seasons, genre, platform)
        cursor.execute(query, values)
        db.commit()
        cursor.close()
        db.close()
        return jsonify({'message': 'Series added successfully'})
    except mysql.connector.Error as e:
        return jsonify({'error': f'Database error: {e}'}), 500

@app.route('/api/series/<int:series_id>', methods=['DELETE'])
def delete_series(series_id):
    user_id = request.args.get('user_id')

    db = get_db_connection()
    if db is None:
        return jsonify({'error': 'Database connection error'}), 500
    
    try:
        cursor = db.cursor()
        query = "DELETE FROM series WHERE series_id = %s AND user_id = %s"
        cursor.execute(query, (series_id, user_id))
        db.commit()
        cursor.close()
        db.close()
        return jsonify({'message': 'Series deleted successfully'})
    except mysql.connector.Error as e:
        return jsonify({'error': f'Database error: {e}'}), 500

@app.route('/api/platforms', methods=['GET'])
def get_platforms():
    user_id = request.args.get('user_id')

    db = get_db_connection()
    if db is None:
        return jsonify({'error': 'Database connection error'}), 500
    
    try:
        cursor = db.cursor()
        query = "SELECT DISTINCT platform FROM series WHERE user_id = %s ORDER BY platform"
        cursor.execute(query, (user_id,))
        platforms = [row[0] for row in cursor.fetchall()]
        cursor.close()
        db.close()
        return jsonify(platforms)
    except mysql.connector.Error as e:
        return jsonify({'error': f'Database error: {e}'}), 500

@app.route('/api/genres', methods=['GET'])
def get_genres():
    user_id = request.args.get('user_id')

    db = get_db_connection()
    if db is None:
        return jsonify({'error': 'Database connection error'}), 500
    
    try:
        cursor = db.cursor()
        query = "SELECT DISTINCT genre FROM series WHERE user_id = %s ORDER BY genre"
        cursor.execute(query, (user_id,))
        genres = [row[0] for row in cursor.fetchall()]
        cursor.close()
        db.close()
        return jsonify(genres)
    except mysql.connector.Error as e:
        return jsonify({'error': f'Database error: {e}'}), 500

@app.route('/api/series/<int:series_id>/rating', methods=['PUT'])
def update_rating(series_id):
    user_id = request.form.get('user_id')
    rating = request.form.get('rating')

    db = get_db_connection()
    if db is None:
        return jsonify({'error': 'Database connection error'}), 500

    try:
        cursor = db.cursor()
        query = "UPDATE series SET rating = %s WHERE series_id = %s AND user_id = %s"
        cursor.execute(query, (rating, series_id, user_id))
        db.commit()
        cursor.close()
        db.close()
        return jsonify({'message': 'Rating updated successfully'})
    except mysql.connector.Error as e:
        return jsonify({'error': f'Database error: {e}'}), 500

if __name__ == '__main__':
    app.run(debug=True)