import mysql.connector
import json
from sentence_transformers import SentenceTransformer
import hnswlib
import numpy as np
import pandas as pd

# Kết nối đến cơ sở dữ liệu MySQL
conn = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="",
    database="luanvan"
)
cursor = conn.cursor()

# Tạo model để chuyển đổi mô tả thành vector hóa
model = SentenceTransformer('sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2')
# Lấy các mô tả và chuyên ngành từ bảng user_nds
cursor.execute("SELECT id, name, description, chuyende FROM user_nds WHERE possition = 0")
user_data = cursor.fetchall()

# Tạo một danh sách để lưu vector nhúng
embedding_data = []

# Vector hóa mô tả và chuyên ngành và lưu vào danh sách
for user_id, name, description, chuyende in user_data:
    embeddings = {}  # Tạo từ điển để lưu embedding cho mỗi trường
    if description:  # Kiểm tra nếu mô tả không rỗng
        # Chuyển đổi mô tả thành vector nhúng
        description_embedding = model.encode(description)
        embeddings['description_embeddings'] = description_embedding.tolist()  # Lưu vector mô tả

    if chuyende:  # Kiểm tra nếu chuyên ngành không rỗng
        # Chuyển đổi chuyên ngành thành vector nhúng
        major_embedding = model.encode(chuyende)
        embeddings['major_embeddings'] = major_embedding.tolist()  # Lưu vector chuyên ngành

    # Thêm vào danh sách dữ liệu nhúng nếu có ít nhất một embedding
    if embeddings:  # Kiểm tra nếu có ít nhất một trường nhúng
        embedding_data.append({
            'id': user_id,
            'name': name,
            'description': description,
            'chuyende': chuyende,
            'embeddings': embeddings  # Lưu từ điển embeddings
        })

# Lưu dữ liệu nhúng vào file JSON
with open('C:\\xampp\\htdocs\\luanvan_tn\\user_embeddings_3.json', 'w', encoding='utf-8') as f:
    json.dump(embedding_data, f, ensure_ascii=False, indent=4)


# Đọc lại dữ liệu từ file JSON
with open('C:\\xampp\\htdocs\\luanvan_tn\\user_embeddings_3.json', 'r', encoding='utf-8') as f:
    embedding_data = json.load(f)

# Chuyển embeddings thành dạng numpy array
user_ids = [item['id'] for item in embedding_data]
names = [item['name'] for item in embedding_data]

# Lưu các vector hóa cho mô tả và chuyên đề
description_embeddings = np.array([item['embeddings'].get('description_embeddings') for item in embedding_data if 'description_embeddings' in item['embeddings']])
specialization_embeddings = np.array([item['embeddings'].get('major_embeddings') for item in embedding_data if 'major_embeddings' in item['embeddings']])

# Kết hợp hai vector bằng cách lấy trung bình
# Lưu lại các user_ids tương ứng với các embeddings hợp lệ
valid_user_ids = []
combined_embeddings = []

# Chỉ thêm những người dùng có cả hai embedding
for i, user_id in enumerate(user_ids):
    if i < len(description_embeddings) and i < len(specialization_embeddings):
        combined_embedding = (description_embeddings[i] + specialization_embeddings[i]) / 2
        combined_embeddings.append(combined_embedding)
        valid_user_ids.append(user_id)

# Chuyển đổi combined_embeddings thành numpy array
combined_embeddings = np.array(combined_embeddings)

# Số lượng người dùng hợp lệ
num_users = len(valid_user_ids)

# Tạo index với HNSW cho các vector kết hợp
index_combined = hnswlib.Index(space='cosine', dim=combined_embeddings.shape[1])
index_combined.init_index(max_elements=num_users, ef_construction=200, M=16)
index_combined.add_items(combined_embeddings, valid_user_ids)

# Thiết lập số lượng lân cận gần nhất (k)
k = 10

# Tạo danh sách chứa kết quả
results = []

# Tìm người dùng gần nhất cho mỗi người dựa trên vector kết hợp
for i, user_id in enumerate(valid_user_ids):
    # Tìm k người gần nhất dựa trên cosine similarity cho vector kết hợp
    labels_combined, distances_combined = index_combined.knn_query(combined_embeddings[i], k=k)
    nearest_users_combined = []
    
    user_name = names[user_ids.index(user_id)]  # Lấy tên của người dùng hiện tại
    # Thay thế dòng print bị lỗi
    # print(f"Người dùng {user_id} - {user_name} gần nhất với: ".encode('utf-8', 'replace').decode('utf-8'))
    for label, distance in zip(labels_combined[0], distances_combined[0]):
        if label != user_id:  # Loại bỏ chính người dùng
            if label in valid_user_ids:  # Kiểm tra label có trong user_ids không
                name = names[user_ids.index(label)]  # Tìm tên bằng cách sử dụng user_ids
                nearest_users_combined.append({'user_id': label, 'name': name, 'distance': distance})
                # print(f" - Người dùng {label} - {name} với khoảng cách {distance} ")

    results.append({
        'user_id': user_id,
        'nearest_users_combined': nearest_users_combined
    })

# Chuyển đổi kết quả thành DataFrame để lưu dưới dạng CSV
rows = []
for result in results:
    user_id = result['user_id']
    for nearest_user in result['nearest_users_combined']:
        rows.append({
            'user_id': user_id,
            'nearest_user_id': nearest_user['user_id'],
            'distance': nearest_user['distance'],
        })

# Tạo DataFrame và lưu dưới dạng CSV
df = pd.DataFrame(rows)
df = df.sort_values(by='user_id')
df.to_csv('C:\\xampp\\htdocs\\luanvan_tn\\nguoi_dung_gan_nhat_combined1.csv', index=False, encoding='utf-8')
# print("Kết quả đã được lưu vào nguoi_dung_gan_nhat_combined.csv")
# print("Đã vector hóa cột mô tả và chuyên ngành và lưu vào tệp user_embeddings.json.")
