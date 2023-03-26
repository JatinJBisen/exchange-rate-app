FROM ubuntu:22.04

COPY . /tmp/temp_data

ENV DEBIAN_FRONTEND=noninteractive

RUN /tmp/temp_data/app

EXPOSE 8000
