from mysql.connector import MySQLConnection, Error
from python_mysql_dbconfig import read_db_config


def connect():
    """ Connect to MySQL database """

    db_config = read_db_config()

    try:
        print('Connecting to MySQL database...')
        conn = MySQLConnection(**db_config)

        if conn.is_connected():
            print('connection established.')
        else:
            print('connection failed.')

    except Error as error:
        print(error)

    finally:
        conn.close()
        print('Connection closed.')


if __name__ == '__main__':
    connect()


def get_pokemon(pokename, pokeuid):
    try:
        dbconfig = read_db_config()
        conn = MySQLConnection(**dbconfig)
        cursor = conn.cursor()

        query = "SELECT id FROM pokemon WHERE `pokeuid`=%s "
        args = (pokeuid,)

        cursor.execute(query, args)
        row = cursor.fetchone()
        if row:
            id = row[0]
        else:
            id = insert_pokemon(pokename, pokeuid)

        return id

    except Error as e:
        print(e)

    finally:
        cursor.close()
        conn.close()


def insert_pokemon(pokename, pokeuid):
    query = "INSERT INTO pokemon(`name`, `pokeuid`, `created`) " \
            "VALUES(%s, %s, NOW())"
    args = (pokename, pokeuid)

    try:
        db_config = read_db_config()
        conn = MySQLConnection(**db_config)

        cursor = conn.cursor()
        cursor.execute(query, args)

        conn.commit()
        return cursor.lastrowid

    except Error as error:
        print(error)

    finally:
        cursor.close()
        conn.close()


def insert_pokelocation(pokId, lng, lat, expired):
    query = "INSERT INTO pokemon_location(`pokemon_id`, `lng`, `lat`, expired, created) " \
            "VALUES(%s, %s, %s, FROM_UNIXTIME(%s), NOW())"

    args = (pokId, lng, lat, expired)

    try:
        db_config = read_db_config()
        conn = MySQLConnection(**db_config)

        cursor = conn.cursor()
        cursor.execute(query, args)

        conn.commit()
        return cursor.lastrowid

    except Error as error:
        print(error)

    finally:
        cursor.close()
        conn.close()


def get_task():
    try:
        dbconfig = read_db_config()
        conn = MySQLConnection(**dbconfig)
        cursor = conn.cursor()

        query = "SELECT id, lat, lng FROM location_for_update WHERE blocked = 0 ORDER BY created DESC"

        cursor.execute(query)
        row = cursor.fetchone()
        if row:
            cursor.close()
            conn.close()
            conn = MySQLConnection(**dbconfig)
            cursor.execute('UPDATE location_for_update SET blocked = 1 WHERE id = ' + format(row[0]))
            conn.commit()
            return (row[1], row[2])
        else:
            return False


    except Error as e:
        print(e)


