FROM python:3.10-slim

WORKDIR /app

# Copy requirements dan install dependencies dulu (layer caching)
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy semua file aplikasi
COPY . .

# Expose port Hugging Face Spaces (WAJIB 7860)
EXPOSE 7860

# Jalankan dengan gunicorn (production WSGI server)
CMD ["gunicorn", "--bind", "0.0.0.0:7860", "--workers", "1", "--timeout", "120", "app:app"]
