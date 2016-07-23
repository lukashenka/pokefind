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
    pokename = pokename.encode('UTF-8')

    nidoran_f = u'Nidoran\u2640'.encode('UTF-8')
    nidoran_m = u'Nidoran\u2642'.encode('UTF-8')  

    if pokename.find(nidoran_f) >= 0:
        pokename = 'Nidoran-F'

    if pokename.find(nidoran_m) >= 0:
        pokename = 'Nidoran-M'

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


def insert_pokelocation(pokemon_list):    
    if len(pokemon_list) == 0:
        return

    values = ', '.join(["(%s, %s, %s, FROM_UNIXTIME(%s), NOW())" % (id, lng, lat, expired) 
        for id, lng, lat, expired in pokemon_list])

    query = "INSERT INTO pokemon_location(`pokemon_id`, `lng`, `lat`, expired, created) " \
            "VALUES " + values

    try:
        db_config = read_db_config()
        conn = MySQLConnection(**db_config)

        cursor = conn.cursor()
        cursor.execute(query)

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

        query = "SELECT id, lat, lng FROM location_for_update WHERE blocked = 0 ORDER BY created ASC"

        cursor.execute(query)
        row = cursor.fetchone()
        if row:
#            cursor.close()
#            conn.close()
            conn2 = MySQLConnection(**dbconfig)
            cursor2 = conn2.cursor()
            cursor2.execute('UPDATE location_for_update SET blocked = 1 WHERE id = ' + format(row[0]))
            conn2.commit()
            return (row[0], row[1], row[2])
        else:
            return False

    except Error as e:
        print(e)


def get_generation_log_id(update_location_id):
    try:

        dbconfig = read_db_config()
        conn = MySQLConnection(**dbconfig)
        cursor = conn.cursor()

        query = "SELECT id FROM generation_log WHERE update_location_id = %s"
        args = (update_location_id,)
        cursor.execute(query, args)
        row = cursor.fetchone()
        if row:
            return row[0]
        else:
            query = "INSERT INTO generation_log(`update_location_id`) " \
                        "VALUES(%s)"
            db_config = read_db_config()
            conn = MySQLConnection(**db_config)
            cursor = conn.cursor()
            cursor.execute(query, (update_location_id,))
            conn.commit()
            return cursor.lastrowid

    except Error as error:
        print(error)

    finally:
        cursor.close()
        conn.close()


def set_step(id, steps, curStep):
    try:

        dbconfig = read_db_config()
        conn = MySQLConnection(**dbconfig)
        cursor = conn.cursor()

        query = "UPDATE generation_log SET current_step = %s, steps = %s WHERE id = %s"
        db_config = read_db_config()
        conn = MySQLConnection(**db_config)
        cursor = conn.cursor()
        steps = format(steps)
        cursor.execute(query, (curStep, steps, id))
        conn.commit()
        return cursor.lastrowid

    except Error as error:
        print(error)

    finally:
        cursor.close()
        conn.close()

def set_step_status(id, done, fail):
    try:

        dbconfig = read_db_config()
        conn = MySQLConnection(**dbconfig)
        cursor = conn.cursor()

        query = "UPDATE generation_log SET  done= %s,  fail= %s WHERE id = %s"

        args = (done, fail, id)

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
