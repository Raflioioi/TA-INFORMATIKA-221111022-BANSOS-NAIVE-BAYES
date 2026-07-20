FROM python:3.11-slim

WORKDIR /app

# Copy requirements and install dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy the api directory code
COPY . .

# Expose FastAPI port
EXPOSE 5000

# Command to run FastAPI server
CMD ["python", "app.py"]
