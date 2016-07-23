import math

def get_delta(lat1, lng1, lat2, lng2, radius):    
    delta_lat = (lat2 - lat1) / 180 * math.pi
    delta_lng = (lng2 - lng1) / 180 * math.pi

    a = math.sin(delta_lat / 2) * math.sin(delta_lat / 2) \
        + math.cos(lat1 / 180 * math.pi) * math.cos(lat2 / 180 * math.pi) \
        * math.sin(delta_lng / 2) * math.sin(delta_lng / 2)
    c = 2 * math.atan2(math.sqrt(a), math.sqrt(1 - a))

    return radius / (6371e3 * c)

def check_in_list(list_values, value1, value2):
    res = False
    for x, y in list_values:
        if  value1 == x and value2 == y:
            res = True
            break
    return res

def check_in_radius(lat_original, lng_original, lat, lng, max_lat, max_lng):
    check = math.pow((lat - lat_original), 2) / math.pow((max_lat), 2) \
        + math.pow((lng - lng_original), 2) / math.pow((max_lng), 2)

    if check <= 1:
        return True
    else:
        return False

def get_coords(lat, lng, radius_scana = 300, coefficient = 1):
    '''
    lat, lng, radius_scana - should not cause problems
    coefficient:
        1 - normal search, find all Pokemon (transfer coefficient below is necessary only if there is a problem in the search)
        2 - very rare find
        ----------------------------------
        0.5 - 77 points at a radius of 500
        1 - 21 points at a radius of 500
        1.5 - 9 points at a raduis of 500
        2 - 5 points at a radius of 500
        ----------------------------------
    '''
    lat = round(lat, 6)
    lng = round(lng, 6)

    standart_radius_signature = 140
    ration = 0.714

    radius_signature = round(standart_radius_signature * ration * coefficient)
    point_count = radius_scana / (radius_signature * 2)

    if int(point_count) == 0:
        point_count = 1

    max_lat = get_delta(lat, lng, lat + 1, lng, radius_scana)
    max_lng = get_delta(lat, lng, lat, lng + 1, radius_scana)

    delta_lat = max_lat / point_count
    delta_lng = max_lng / point_count

    result = [(lat, lng)]

    if point_count == 1:
        return result

    for i in range(int(point_count) + 1):
        for j in range(int(point_count) + 1):
            point_lat = round(lat + (delta_lat * i), 6)
            point_lng = round(lng + (delta_lng * j), 6)
            if check_in_radius(lat, lng, point_lat, point_lng, max_lat, max_lng):
                if not check_in_list(result, point_lat, point_lng):
                    result.append((point_lat, point_lng))
                    point_lat = round(lat + (delta_lat * i), 6)
                    point_lng = round(lng - (delta_lng * j), 6)
                    if not check_in_list(result, point_lat, point_lng):
                        result.append((point_lat, point_lng))
                    point_lat = round(lat - (delta_lat * i), 6)
                    point_lng = round(lng + (delta_lng * j), 6)
                    if not check_in_list(result, point_lat, point_lng):
                        result.append((point_lat, point_lng))
                    point_lat = round(lat - (delta_lat * i), 6)
                    point_lng = round(lng - (delta_lng * j), 6)
                    if not check_in_list(result, point_lat, point_lng):
                        result.append((point_lat, point_lng))
    return result
