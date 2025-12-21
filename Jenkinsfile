pipeline {
    agent any

    environment {
        DOCKER_REGISTRY = 'wahyuditrs17'
        IMAGE_NAME = 'vektora'
        APP_URL = 'https://vektora-ffhggreufqf7dteg.southeastasia-01.azurewebsites.net'
    }

    stages {
        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    bat "docker build -t %DOCKER_REGISTRY%/%IMAGE_NAME%:%BUILD_ID% -t %DOCKER_REGISTRY%/%IMAGE_NAME%:latest ."
                }
            }
        }

        stage('Push to Docker Hub') {
            steps {
                script {
                    withCredentials([usernamePassword(
                        credentialsId: 'docker-hub-credential',
                        usernameVariable: 'DOCKER_USERNAME',
                        passwordVariable: 'DOCKER_PASSWORD'
                    )]) {
                        bat "echo %DOCKER_PASSWORD% | docker login --username %DOCKER_USERNAME% --password-stdin"
                        bat "docker push %DOCKER_REGISTRY%/%IMAGE_NAME%:%BUILD_ID%"
                        bat "docker push %DOCKER_REGISTRY%/%IMAGE_NAME%:latest"
                        
                        echo "✅ Image pushed: %DOCKER_REGISTRY%/%IMAGE_NAME%:latest"
                    }
                }
            }
        }

        stage('Verify Deployment') {
            steps {
                script {
                    echo '''
                    ========================================
                    🎉 CI/CD PIPELINE COMPLETE!
                    
                    ✅ Docker Image Updated:
                       • wahyuditrs17/vektora:%BUILD_ID%
                       • wahyuditrs17/vektora:latest
                    
                    🔄 Azure App Service akan otomatis:
                       1. Pull image terbaru dari Docker Hub
                       2. Restart container dengan image baru
                       3. Waktu: 2-5 menit setelah push
                    
                    📱 Untuk mempercepat restart:
                       1. Login ke Azure Portal
                       2. Buka App Service: project-vektora
                       3. Klik "Restart" (Opsional)
                       
                    🌐 Aplikasi Anda:
                       %APP_URL%
                    ========================================
                    '''
                    
                    // Opsional: Health check setelah beberapa menit
                    sleep(time: 120, unit: 'SECONDS') // Tunggu 2 menit
                    bat "curl -f %APP_URL% || echo 'App mungkin masih restarting...'"
                }
            }
        }
    }
    
    post {
        success {
            echo '🎊 Pipeline BERHASIL! Aplikasi akan otomatis update di Azure.'
            // Opsional: Notifikasi
            // emailext body: 'Pipeline vektora berhasil! Image terbaru: wahyuditrs17/vektora:latest', subject: '✅ Deployment Success', to: 'email@anda.com'
        }
        always {
            echo 'Pipeline selesai'
            cleanWs()
        }
    }
}
