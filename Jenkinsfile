#!/usr/bin/env groovy

pipeline {

    agent none

    stages {
        stage('Build') {
            agent { label 'some-agent' }
            steps {
                echo 'Building...'
                echo "prepare: ${pwd()}"
            }
        }
        stage('Test') {
            steps {
                echo 'Testing...'
                echo "prepare: ${pwd()}"
            }
        }
    }
}
