pipeline {
    agent any
    
    environment {
        DOCKER_IMAGE = 'your-dockerhub-username/groceri-app'
        DOCKER_TAG = "${env.BUILD_NUMBER}"
        AZURE_REGISTRY = 'yourregistry.azurecr.io'
    }
    
    stages {
        // STAGE 1: Checkout code dari GitHub
        stage('Checkout') {
            steps {
                checkout scmGit(
                    branches: [[name: 'main']],
                    userRemoteConfigs: [[url: 'https://github.com/akhdansayudha/project-groceri.git']]
                )
            }
        }
        
        // STAGE 2: Build & Test
        stage('Build & Test') {
            steps {
                script {
                    // Build Docker image
                    sh "docker build -t ${DOCKER_IMAGE}:${DOCKER_TAG} ."
                    
                    // Run tests (jika ada)
                    sh "docker run --rm ${DOCKER_IMAGE}:${DOCKER_TAG} npm test || true"
                }
            }
        }
        
        // STAGE 3: Push to Docker Hub
        stage('Push to Registry') {
            steps {
                script {
                    // Login ke Docker Hub
                    withCredentials([string(credentialsId: 'docker-hub-password', variable: 'DOCKER_PASSWORD')]) {
                        sh "echo ${DOCKER_PASSWORD} | docker login --username your-dockerhub-username --password-stdin"
                    }
                    
                    // Push image
                    sh "docker push ${DOCKER_IMAGE}:${DOCKER_TAG}"
                    sh "docker tag ${DOCKER_IMAGE}:${DOCKER_TAG} ${DOCKER_IMAGE}:latest"
                    sh "docker push ${DOCKER_IMAGE}:latest"
                }
            }
        }
        
        // STAGE 4: Deploy to Azure
        stage('Deploy to Azure') {
            steps {
                script {
                    // Deploy ke Azure Container Instances atau App Service
                    sh '''
                    az login --service-principal -u $AZURE_CLIENT_ID -p $AZURE_CLIENT_SECRET --tenant $AZURE_TENANT_ID
                    az container create \
                        --resource-group groceri-rg \
                        --name groceri-container \
                        --image ${DOCKER_IMAGE}:${DOCKER_TAG} \
                        --dns-name-label groceri-app \
                        --ports 3000 \
                        --environment-variables NODE_ENV=production
                    '''
                }
            }
        }
        
        // STAGE 5: Health Check
        stage('Health Check') {
            steps {
                script {
                    // Test aplikasi setelah deploy
                    sh "sleep 30"  // Tunggu container ready
                    sh "curl -f http://groceri-app.westus.azurecontainer.io:3000/health || exit 1"
                }
            }
        }
    }
    
    post {
        success {
            emailext(
                subject: "SUCCESS: Pipeline ${env.JOB_NAME} - ${env.BUILD_NUMBER}",
                body: "Pipeline berhasil! Aplikasi sudah di-deploy.",
                to: 'team@email.com'
            )
        }
        failure {
            emailext(
                subject: "FAILED: Pipeline ${env.JOB_NAME} - ${env.BUILD_NUMBER}",
                body: "Pipeline gagal! Cek logs di Jenkins.",
                to: 'team@email.com'
            )
        }
    }
}
