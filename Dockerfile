FROM php:7.2-cli
ENV working_dir /app
COPY . working_dir
WORKDIR working_dir